TARGET1 = /home/hzx/.vim/plugin/

INS_SRC = eyou_work_log.vim. NERD_tree.vim. php-doc.vim. phpLint.vim. 
all:

eyou_work_log.vim.: eyou_work_log.vim
	$(INSTALL1)
NERD_tree.vim.: NERD_tree.vim
	$(INSTALL1)
php-doc.vim.: php-doc.vim
	$(INSTALL1)
phpLint.vim.: phpLint.vim
	$(INSTALL1)
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
