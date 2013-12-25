TARGET1 = /home/hzx/.vim/doc/

INS_SRC = acp.jax. acp.txt. NERD_tree.txt.
all:

acp.jax.: acp.jax
	$(INSTALL1)
acp.txt.: acp.txt
	$(INSTALL1)
NERD_tree.txt.: NERD_tree.txt
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
