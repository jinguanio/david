<?php
// return child process id
function create_daemon($daemon_file = null)
{
    pcntl_signal(SIGTTOU, SIG_IGN);
    pcntl_signal(SIGTTIN, SIG_IGN);
    pcntl_signal(SIGTSTP, SIG_IGN);
    pcntl_signal(SIGHUP, SIG_IGN);

    $pid = pcntl_fork();
    if (0 > $pid) {
        _log('fork child process fail', __LINE__);
        exit(1);
    } elseif ($pid) {
        exit(0);
    }

    if (0 > posix_setsid()) {
        _log('set session leader fail', __LINE__);
        exit(1);
    }

    $pid = pcntl_fork();
    if (-1 === $pid) {
        _log('fork child process fail', __LINE__);
        exit(1);
    } elseif ($pid) {
        return $pid;
    }

    if (!chdir('/tmp')) {
        _log('child process set dir fail', __LINE__);
        exit(1);
    }

    umask(0);

    if (!pcntl_signal(SIGTERM, 'handler_sigterm')) {
        _log('init_daemon, set signal handler for SIGTERM failed.', __LINE__);
        exit(1);
    }

    if (!pcntl_signal(SIGCHLD, 'handler_sigchld')) {
        _log('init_daemon, set signal handler for SIGCHLD failed.', __LINE__);
        exit(1);
    }

    _log('init_daemon success.', __FILE__);
    
    pcntl_exec('/usr/local/eyou/mail/opt/bin/php ' . $daemon_file);
    //exec('php ' . $daemon_file);
    //while (1) {
    //    sleep(3);
    //}
}

function _log($msg, $line)
{
    $msg = date('r') . " [{$line}], {$msg}" . PHP_EOL;
    file_put_contents('daemon.log', $msg, FILE_APPEND);
}

function clear_log()
{
    file_put_contents('daemon.log', '');
}

function handler_sigterm()
{
    _log('catch signal SIGTERM, exiting...', __LINE__);
    if (!pcntl_signal(SIGCHLD, SIG_IGN)) {
        _log('set signal handler for SIGTERM failed.', __LINE__);
    }
    if (!posix_kill(0, SIGTERM)) {
        _log(posix_strerror(posix_get_last_error()), __LINE__);
    }
    while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
        if ($pid < 0) { // 无可回收的进程
            break;
        }
        _log("stop child $pid success.", __LINE__);
    }
    _free_parent(posix_getpid());
    _log('exit by signal SIGTERM', __LINE__);
    exit(0);
}

function handler_sigchld()
{
    _log('catch signal SIGCHLD', __LINE__);
    while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
        if ($pid < 0) { // 无可回收的进程
            break;
        }
        _log("child $pid exit, status: $status.", __LINE__);
        _free_child($pid, $status);
        usleep(100000);
    }
}

function _free_parent()
{
}

function _free_child()
{
}

clear_log();
create_daemon('/tmp/test.php');

