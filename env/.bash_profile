# .bash_profile

# Get the aliases and functions
if [ -f ~/.bashrc ]; then
	. ~/.bashrc
fi

# User specific environment and startup programs
EYOU="/usr/local/eyou/mail"

APP="$EYOU/app"
APP_SBIN="$APP/sbin"
APP_BIN="$APP/bin"

OPT="$EYOU/opt"
OPT_BIN="$OPT/bin"
OPT_SBIN="$OPT/sbin"

PATH=$APP_SBIN:$APP_BIN:$OPT_BIN:$OPT_SBIN:$HOME/bin:$PATH

export PATH
unset USERNAME

# translate into chinese
#export LANG=zh_CN.utf8

ntpdate 210.72.145.44
