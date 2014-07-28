/*
 * =====================================================================================
 *
 *       Filename:  a.c
 *
 *    Description:  Test
 *
 *        Version:  1.0
 *        Created:  07/24/2014 06:30:10 PM
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
#define	EXIT_SUCCESS 0			/*  */

void aprintcf(char *);
void pprintcf(char *);

int
main ( int argc, char *argv[] )
{
    char *str = "libo";
    char str2[] = "bnn";

    printf("========= pointer ======== \n");
    printf("string: libo\n");
    aprintcf(str);
    printf("string: libo\n");
    pprintcf(str);

    printf("\n======== array ======== \n");
    printf("string: bnn\n");
    aprintcf(str2);
    printf("string: bnn\n");
    pprintcf(str2);

    return EXIT_SUCCESS;
}				/* ----------  end of function main  ---------- */

void aprintcf(char *s)
{
    int i = 0;
    char c;

    while ((c = s[i++]) != '\0') {
        printf("%d\n", c);
        printf("%c\n", c);
        printf("%c\n", c-32);
    }
}

void pprintcf(char *s)
{
    char c;

    while ((c = *s++) != '\0') {
        printf("%d\n", c);
        printf("%c\n", c);
        printf("%c\n", c-32);
    }
}
