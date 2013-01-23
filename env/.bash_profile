# .bash_profile

# Get the aliases and functions
if [ -f ~/.bashrc ]; then
	. ~/.bashrc
fi

# User specific environment and startup programs
EYOU="/usr/local/eyou/mail"
EYOU_APP="$EYOU/app"
EYOU_SBIN="$EYOU_APP/sbin"
EYOU_BIN="$EYOU_APP/bin"

PATH=$EYOU_SBIN:$EYOU_BIN:$PATH:$HOME/bin

export PATH
unset USERNAME
