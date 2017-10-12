#!/bin/bash

echo "Autofixing coding violations"

# Fix violations using php-cs-fixer
php ./bin/php-cs-fixer -vvv --no-interaction --fixers=psr0,encoding,short_tag,braces,elseif,eof_ending,function_call_space,function_declaration,indentation,line_after_namespace,linefeed,lowercase_constants,lowercase_keywords,method_argument_space,multiple_use,parenthesis,php_closing_tag,single_line_after_imports,trailing_spaces,visibility,blankline_after_open_tag,double_arrow_multiline_whitespaces,duplicate_semicolon,empty_return,extra_empty_lines,include,join_function,list_commas,multiline_array_trailing_comma,namespace_no_leading_whitespace,new_with_braces,no_blank_lines_after_class_opening,no_empty_lines_after_object_operator,operators_spaces,remove_leading_slash_use,remove_lines_between_uses,return,self_accessor,single_array_no_trailing_comma,single_blank_line_before_namespace,single_quote,spaces_before_semicolon,spaces_cast,standardize_not_equal,ternary_spaces,trim_array_spaces,unalign_double_arrow,unalign_equals,unary_operators_spaces,unused_use,whitespacy_lines,concat_with_spaces,ereg_to_preg,header_comment,multiline_spaces_before_semicolon,newline_after_open_tag,ordered_use,php4_constructor,php_unit_construct,short_echo_tag fix src

# Fix violations using phpcbf
php ./vendor/squizlabs/php_codesniffer/scripts/phpcbf src/ -n -p --standard=PSR2 --ignore=*Test.php --ignore=*/Entity/* --ignore=*/js/libs/*