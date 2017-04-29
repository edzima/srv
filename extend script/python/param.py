#!/usr/bin/python

import sys, getopt

def start(run):
	while run:
		print '1'

		

def main(argv):
   
   if(argv[1]=='start'):
       start(1)
   elif (argv[1]=='stop'):
       start(0)
	   
if __name__ == "__main__":
   main(sys.argv)