TARGET1 = /home/hzx/.vim/

SUBDIRS = autoload doc nerdtree_plugin plugin syntax 

all:

# Do not modify
INSTALL1 = /usr/bin/install -m 644 -o hzx -g hzx $< $(TARGET1)

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
		chown hzx:hzx $(TARGET1); \
	fi; 

install: make_dir $(INS_SRC)
	@$(INS_DIRS)
