set ru
set autochdir
set sts=4
set cinoptions=:0,p0,t0
set cinw=if,else,while,do,for,switch,case
set formatoptions=tqo
set cin
set hls
set incsearch
set et
set ts=4
set sw=4
set aw
set foldmethod=marker
set showcmd
set statusline=%F%m%r%h%w\[LINE=%04l]\[COL=%04v]\[LEN=%L]
set laststatus=2
set autoread
set history=400
set fenc=utf-8
set encoding=utf-8
set fileencodings=utf-8,gbk,cp936,latin-1

syn on
filetype plugin on
filetype plugin indent on

" ======= 引号 && 括号自动匹配 ======= "
":inoremap ( ()<ESC>i
":inoremap ) <c-r>=ClosePair(')')<CR>
":inoremap { {}<ESC>i
":inoremap } <c-r>=ClosePair('}')<CR>
":inoremap [ []<ESC>i
":inoremap ] <c-r>=ClosePair(']')<CR>
":inoremap " ""<ESC>i
":inoremap ' ''<ESC>i
":inoremap ` ``<ESC>i
"
"function ClosePair(char)
"  if getline('.')[col('.') - 1] == a:char
"     return "\<Right>"
"  else
"     return a:char
"  endif
"endf

" ============ link ============
:nmap fw :w<CR>
:nmap zw :wqa<CR>
:nmap zq :qa!<CR>
:nmap ,s :source ~/.vimrc<CR>
:nmap ,e :!

" ============ php syntax ============
function! PHPCheckSyntax()
    setlocal makeprg=/usr/local/eyou/mail/opt/bin/php\ -l\ -n\ -d\ html_errors=off
    setlocal shellpipe=>

    " Use error format for parsing PHP error output
    setlocal errorformat=%m\ in\ %f\ on\ line\ %l
    make %
endfunction

" Perform :PHPCheckSyntax()
map <F5> :call PHPCheckSyntax()<CR>

