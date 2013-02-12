# .bashrc

# User specific aliases and functions

alias rm='rm -i'
alias cp='cp -i'
alias mv='mv -i'

alias vi='vim'
alias j='jobs'
alias ll='ls -lh'
alias la='ls -alh'
alias lm='ls -alh | more'
alias c='clear'

alias mysql='/usr/local/eyou/mail/opt/mysql/bin/mysql -uroot -S /usr/local/eyou/mail/run/em_mysql.sock -D eyou_mail'
alias mysql_idx='/usr/local/eyou/mail/opt/mysql/bin/mysql -uroot -S /usr/local/eyou/mail/run/em_mysql_index.sock -D eyou_mail_index'

# Source global definitions
if [ -f /etc/bashrc ]; then
	. /etc/bashrc
fi

. $HOME/.git-completion.bash
