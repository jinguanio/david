" File:         phpLint.vim
" Author:       Joe Stelmach (joe@zenbe.com)  borrow by satan
" Version:      0.2
" Description:  phpLint.vim allows the php Lint (zend ) program 
"               from http://www.phplint.com/ to be tightly integrated 
"               with vim.  The contents of a php file will be passed 
"               through the zend code Analyzing program after the file's buffer is saved.  
"               Any lint warnings will be placed in the quickfix window.  
"               php Lint must be installed on your system for this 
"               plugin to work properly. 
" Last Modified: Oct 31, 2011

if !exists("phplint_command")
  let phplint_command = 'zca'
endif

if !exists("phplint_command_options")
  let phplint_command_options = ''
endif

if !exists("phplint_highlight_color")
  let phplint_highlight_color = 'DarkMagenta'
endif

" set up auto commands
autocmd BufWritePost,FileWritePost *.php call PhpLint()
autocmd BufWinLeave * call s:MaybeClearCursorLineColor()

" Runs the current file through php lint and 
" opens a quickfix window with any warnings
function PhpLint() 
  " run php lint on the current file
  let current_file = shellescape(expand('%:p'))
  let cmd_output = system(g:phplint_command . ' ' . g:phplint_command_options . ' ' . current_file)

  " if some warnings were found, we process them
  let errorexst = match(cmd_output, "syntax error") " sa detect error

  let cmd_output = substitute(cmd_output, "^Zend[^\n]*\n", "" , "g")  " del the line tip
  let cmd_output = substitute(cmd_output, "^\s*Analyzing[^\n]*\n", "" , "g")  " del the line tip
			  
  if strlen(cmd_output) > 0
	 let cmd_output = substitute(cmd_output, "line ", "" , "g")  " del the line tip

    " ensure proper error format
    let s:errorformat = "%f(%l):\%m^M"

    " write quickfix errors to a temp file 
    let quickfix_tmpfile_name = tempname()
    exe "redir! > " . quickfix_tmpfile_name
      silent echon cmd_output
    redir END

    " read in the errors temp file 
    execute "silent! cfile " . quickfix_tmpfile_name

    " change the cursor line to something hard to miss 
    call s:SetCursorLineColor()

    " open the quicfix window
	if( errorexst > 0 )  " by sa , only error show
		botright copen
	endif
    let s:qfix_buffer = bufnr("$")

    " delete the temp file
    call delete(quickfix_tmpfile_name)

  " if no php warnings are found, we revert the cursorline color
  " and close the quick fix window
  else 
    call s:ClearCursorLineColor()
    if(exists("s:qfix_buffer"))
      cclose
      unlet s:qfix_buffer
    endif
  endif
endfunction

" sets the cursor line highlight color to the error highlight color 
function s:SetCursorLineColor() 
  " check for disabled cursor line
  if(!exists("g:phplint_highlight_color") || strlen(g:phplint_highlight_color) == 0) 
    return 
  endif

  call s:ClearCursorLineColor()
  let s:highlight_on = 1 

  " find the current cursor line highlight info 
  redir => l:highlight_info
    silent highlight CursorLine
  redir END

  " find the guibg property within the highlight info (if it exists)
  let l:start_index = match(l:highlight_info, "guibg")
  if(l:start_index > 0)
    let s:previous_cursor_guibg = strpart(l:highlight_info, l:start_index)

  elseif(exists("s:previous_cursor_guibg")) 
    unlet s:previous_cursor_guibg
  endif

  execute "highlight CursorLine guibg=" . g:phplint_highlight_color
endfunction

" Conditionally reverts the cursor line color based on the presence
" of the quickfix window
function s:MaybeClearCursorLineColor()
  if(exists("s:qfix_buffer") && s:qfix_buffer == bufnr("%"))
    call s:ClearCursorLineColor()
  endif
endfunction

" Reverts the cursor line color
function s:ClearCursorLineColor()
  " only revert if our highlight is currently enabled
  if(exists("s:highlight_on") && s:highlight_on) 
    let s:highlight_on = 0 

    " if a previous cursor guibg color was recorded, we use it
    if(exists("s:previous_cursor_guibg")) 
      execute "highlight CursorLine " . s:previous_cursor_guibg
      unlet s:previous_cursor_guibg

    " otherwise, we clear the curor line highlight entirely
    else
      highlight clear CursorLine 
    endif
  endif
endfunction
