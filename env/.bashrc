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

# Source global definitions
if [ -f /etc/bashrc ]; then
	. /etc/bashrc
fi

. $HOME/.git-completion.bash
