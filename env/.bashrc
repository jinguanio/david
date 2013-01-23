# .bashrc

# User specific aliases and functions

alias rm='rm -i'
alias cp='cp -i'
alias mv='mv -i'

alias vi='vim'
alias j='jobs'
alias ll='ls -lh'
alias la='ls -alh'
alias c='clear'

alias mysql='/usr/local/eyou/mail/opt/mysql/bin/mysql -uroot -S /usr/local/eyou/mail/run/em_mysql.sock'

# Source global definitions
if [ -f /etc/bashrc ]; then
	. /etc/bashrc
fi

. $HOME/.git-completion.bash
