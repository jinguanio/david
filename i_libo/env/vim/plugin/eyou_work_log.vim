" ============================================================================
" File:        eyou_work_log.vim
" Description: 在 vim 中写 eYou Tracker 工作日志的 vim 插件
" Maintainer:  xutiangong <xutiangong at eyou dot net>
" Last Change: 2012-10-30
" License:     GPL
"
" Config:
"   1. 把此 eyou_work_log.vim 文件存放到你的 vim 插件目录中 (~/.vim/plugin)
"
"   2. 登录 http://code.eyou.net/tracker
"      进入 [个人资料] -> [个人API设置] 设置你的 Token,
"      记下页面显示的你设置好的 Token 的 md5 值, 例如
"      8aba983eb7aeaed8092a2a1372814703
"
"   3. 查看你终端(例如 putty, SecureCRT, Xshell 等) 的字符集, 例如:
"      UTF-8, GBK, 一般默认的终端字符集配置是 GBK
"
"   4. 在你的 .vimrc (~/.vimrc) 中添加如下几行设置参数
"
"      let g:eyou_work_log_user="你的 Tracker UserName"
"      let g:eyou_work_log_token="你的 Token"
"      let g:eyou_work_log_charset="你的终端字符集 Charset"
"
"      例如:
"        UserName: eyoubest,
"        Token:    8aba983eb7aeaed8092a2a1372814703
"        Charset:  UTF-8
"      那么:
"        let g:eyou_work_log_user="eyoubest"
"        let g:eyou_work_log_token="8aba983eb7aeaed8092a2a1372814703"
"        let g:eyou_work_log_charset="UTF-8"
"
" Usage:
"   约束:
"     1. 工作日志 Tracker 每月条目的主题必须严格符合如下格式:
"        年月必须是 YYYY-MM 格式, 年 4 位, 月 2 位, 年月用英文 - 连接
"        例如: 小明 2012-01 工作日志
"
"     2. 工作日志 Tracker 每日条目的所属日期用如下优先级确定:
"        首先优先获取第一行是否是 YYYY-MM-DD 格式, 如果是则使用第一行标记的日期,
"        如果第一行不是 YYYY-MM-DD 则获取此条目的创建时间.
"
"        例如下面日志即使是 2012年1月2日 写的, 但却是 2012年1月1日 的工作日志:
"          2012-01-01
"          修改邮件不能投递的 bug
"
"   获取日志:
"     1. 打开一个新的 vim 窗口, 或者创建一个分屏
"     2. 第 1 行写上你要获取的日志的日期, 日期的格式必须是如下 2 种:
"        YYYY-MM-DD 和 today
"        YYYY-MM-DD 表示你要获取的日志的日期, today 表示你要获取当天的日志
"        today 对于你随时获取并修改当天的日志非常方便
"     3. 使用 :LogGet 命令来获取日志, 执行此命令之后日志会被显示在当前 vim 窗口中
"     4. 如果没有获取到会给出 Error 提示
"
"   添加和修改日志:
"     1. 打开一个新的 vim 窗口, 或者创建一个分屏
"     2. 第 1 行写上你要修改的日志的日期, 日期的格式必须是如下 2 种:
"        YYYY-MM-DD 和 today
"        YYYY-MM-DD 表示你要修改的日志的日期, today 表示你要修改当天的日志
"        today 对于你随时获取并修改当天的日志非常方便
"     3. 在第 2 行开始写入你的日志, 日志可以是多行
"     3. 使用 :LogAdd 命令来添加或修改(有则修改, 没有则添加)
"     4. 添加失败会给出 Error 提示
"     5. 支持自动创建不存在的 Tracker 月条目
"
"   技巧:
"     写当天的日志:
"       1. 打开一个新的 vim 窗口, 或者用 :new 命令创建一个新的分屏
"       2. 第 1 行写上 today, 第 2 行开始写日志内容, 执行 :LogAdd 添加日志
"
"     补写之前落下的日志 (2012-10-01):
"       1. 打开一个新的 vim 窗口, 或者用 :new 命令创建一个新的分屏
"       2. 第 1 行写上 2012-10-01, 第 2 行开始写日志内容, 执行 :LogAdd 补日志
"
"     随时修改当天的日志, 防止在下班要写的时候忘记了今天都干啥了:
"       1. 打开一个新的 vim 窗口, 或者用 :new 命令创建一个新的分屏
"       2. 第 1 行写上 today, 然后执行 :LogGet 命令获取当天的日志
"       3. 修改当天的日志, 第 1 行 today 要保留, 执行 :LogAdd 修改日志
"
" FAQ:
"   * 报错中包含 No module named simplejson 错误信息
"       此脚本需要 python 的 simplejson 模块, 报此错误说明你系统中没有
"       这个模块, 如果是 CentOS 系统可以用 yum install python-simplejson
"       命令来安装, 其他系统请 google 安装方法.
"
"   * 日志没有被添加到已存在的 Tracker 当月条目中, 而是被新创建了一个月条目
"       请检查你的 Tracker 月条目的主题是否符合上述的 [约束] 规则.
"
"   * 本应该修改日志, 但是却创建了一条新的
"       请检查你的日志日条目的所属日期是否符合上述的 [约束] 规则.
"
"   * 无法获取到某天的日志
"       请检查你日志所在的月条目和日志条目是否符合上述的 [约束] 规则.
"
"   * 被添加的日志是空的或者是乱码
"       请检查你的终端字符集 Charset (g:eyou_work_log_charset) 设置是否正确
"
"   * 其他
"       请检查你的 g:eyou_work_log_user 和 g:eyou_work_log_token 设置是否正确
"       如仍无法解决请与我联系.
"
"  Thanks:
"    我的第一个 vim 插件, 请多多指教.
"    Enjoy *^_^*
"
" ============================================================================

if !has('python')
    echo "Error: Required vim compiled with +python"
    finish
endif

" 判断变量定义
if !exists("g:eyou_work_log_user")
    let g:eyou_work_log_user=""
    echo "Error: you are not set 'eyou_work_log_user', please set it in your ~/.vimrc"
    finish
endif

if !exists("g:eyou_work_log_token")
    echo "Error: you are not set 'eyou_work_log_token', please set it in your ~/.vimrc"
    finish
endif

if !exists("g:eyou_work_log_charset")
    let g:eyou_work_log_charset="UTF-8"
endif

command! -nargs=0 LogGet call EyouWorkLogGet()
command! -nargs=0 LogSet call EyouWorkLogAdd()
command! -nargs=0 LogAdd call EyouWorkLogAdd()

" 定义变量
let g:eyou_work_log_url="http://code.eyou.net/tracker/plugin.php?page=EMUserAPI/api_work_log&user=%s&token=%s"

" 获取日志函数
function! EyouWorkLogGet()
    call EyouWorkLog("get")
endfunction

" 添加日志函数
function! EyouWorkLogAdd()
    call EyouWorkLog("add")
endfunction

function! EyouWorkLog(ArgAction)
" 载入 python
python << ENDPY
import vim, urllib, urllib2, sys, simplejson, re


try:
    arg_action = vim.eval("a:ArgAction")

    eyou_work_log_charset = vim.eval("g:eyou_work_log_charset");
    eyou_work_log_user = vim.eval("g:eyou_work_log_user")
    eyou_work_log_token = vim.eval("g:eyou_work_log_token")
    eyou_work_log_url = vim.eval("g:eyou_work_log_url");
    eyou_work_log_url = vim.eval("g:eyou_work_log_url");

    url_api_base = eyou_work_log_url % (urllib.quote(eyou_work_log_user), urllib.quote(eyou_work_log_token))

    # 读取第一行
    eyou_work_log_date = vim.current.buffer[0].decode(eyou_work_log_charset)
    if (None == re.match('\d{4}-\d{2}-\d{2}', eyou_work_log_date)) and ('today' != eyou_work_log_date):
        #-- start libo@eyou.net
        #print 'Error: your first line must log date, and must be match YYYY-MM-DD or today.'
        #sys.exit()
        eyou_work_log_date = 'today'
        #-- end
    eyou_work_log_date_locale = eyou_work_log_date.encode(eyou_work_log_charset);

    if 'get' == arg_action:
        url_api_get = url_api_base + "&action=get&date=%s" % (eyou_work_log_date)

        # 调用 api 获取日志
        response = urllib2.urlopen(url_api_get).read()
        json_response = simplejson.loads(response)
        if 0 != json_response[0]:
            print 'Error: can not get your %s log, message: %s' % (eyou_work_log_date_locale, json_response[1].encode(eyou_work_log_charset))
            sys.exit()

        del vim.current.buffer[:]

        log_lins = json_response[2].encode(eyou_work_log_charset).replace('\r\n', '\n').split('\n')
        i = 0
        is_write_buffer = 0
        for line in log_lins:
            i = i + 1

            if '' == line:
                continue

            is_write_buffer = 1

            if 1 == i:
                if None == re.match('\d{4}-\d{2}-\d{2}', '2012'):
                    vim.current.buffer[0] = eyou_work_log_date_locale
                    vim.current.buffer.append(line)
                else:
                    vim.current.buffer[0] = line
            else:
                vim.current.buffer.append(line)

        if 0 == is_write_buffer:
            vim.current.buffer[0] = eyou_work_log_date_locale

        print 'Success: get %s log success' % (eyou_work_log_date_locale)

    elif 'add' == arg_action:
        log_data = ''
        i = 0
        for line in vim.current.buffer:
            i = i + 1

            #-- start libo@eyou.net
            #if 1 == i:
            if (1 == i) and ('today' == line.decode(eyou_work_log_charset)):
            #-- end
                continue

            log_data = log_data + '\r\n' + line.decode(eyou_work_log_charset)

        log_data = urllib.quote(log_data.strip().encode('UTF-8'))
        url_api_add = url_api_base + "&action=add&date=%s&log=%s" % (eyou_work_log_date, log_data)

        # 调用 api 添加日志
        response = urllib2.urlopen(url_api_add).read()
        json_response = simplejson.loads(response)
        if 0 != json_response[0]:
            print 'Error: can not add %s log, message: %s' % (eyou_work_log_date_locale, json_response[1].encode(eyou_work_log_charset))
            sys.exit()

        print 'Success: add %s log success' % (eyou_work_log_date_locale)
    else:
        print 'Error: action wrong, not get and not add?'
except Exception, e:
    print e

ENDPY

endfunction

