/*
 * =====================================================================================
 *
 *       Filename:  return_point.c
 *
 *    Description:  返回函数指针
 *
 *        Version:  1.0
 *        Created:  2014年07月28日 21时16分41秒
 *       Revision:  none
 *       Compiler:  gcc
 *
 *         Author:  Dr. Fritz Mehner (mn), mehner@fh-swf.de
 *        Company:  FH Südwestfalen, Iserlohn
 *
 * =====================================================================================
 */


#include	<stdio.h>
#include	<stdlib.h>
#include	<string.h>

float add(float a,float b) {return a+b;} 
float minus(float a,float b) {return a-b;} 
float multiply(float a,float b) {return a*b;} 
float divide(float a,float b) {return a/b;} 

float(* FunctionMap(char op) )(float,float) 
{ 
    switch(op) { 
    case '+': 
        return add; 
        break; 
    case '-': 
        return minus; 
        break; 
    case '*': 
        return multiply; 
        break; 
    case '\\': 
        return divide; 
        break; 
    default: 
        exit(1); 
    } 
} 

int
main ( int argc, char *argv[] )
{
    float a=10,b=5; 
    char ops[]={'+','-','*','\\'}; 
    int len=strlen(ops); 
    int i=0; 
    float (*returned_function_pointer)(float,float); 

    for(i=0;i<len;i++) { 
        returned_function_pointer=FunctionMap(ops[i]); 
        printf("the result caculated by the operator %c is %f\n",ops[i],returned_function_pointer(a,b)); 
    } 

    return EXIT_SUCCESS;
}				/* ----------  end of function main  ---------- */

