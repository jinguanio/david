TARGET1 = /home/hzx/

SUBDIRS = 
INS_SRC = 
INS_ENC_PHP = 
INS_ENC_JS  = 
INS_ENC_CSS = 
INS_UNENC_PHP = 
INS_UNENC_JS  = 
INS_UNENC_CSS = 

PATH_JAVA = /usr/local/eyou/devmail/opt/java/bin/java
PATH_JAR_YUI = /usr/local/eyou/devmail/tools/yuicompressor/yuicompressor.jar

all:


# Do not modify
INSTALL1 = /usr/bin/install -m 664 -o root -g root $< $(TARGET1)

INS_DIRS = \
	if test "$(SUBDIRS)"; then \
		echo "Install Dirs: $(SUBDIRS)"; \
		for i in `echo $(SUBDIRS)`; do \
			make install -C $$i; \
		done; \
	fi;

make_dir:
	-@if test ! -d $(TARGET1); then \
		echo "Make Dir: $(TARGET1)"; \
		mkdir -m 755 -p $(TARGET1); \
		chown root:root $(TARGET1); \
	fi; 

copy: make_dir $(INS_UNENC_PHP) $(INS_UNENC_JS) $(INS_UNENC_CSS) $(INS_SRC)

install: make_dir $(INS_ENC_PHP) $(INS_ENC_JS) $(INS_ENC_CSS) $(INS_SRC)
	@$(INS_DIRS)