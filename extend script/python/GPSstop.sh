#!/bin/bash

STATUS=$(gpio read 1)

if [ $STATUS = 1 ]
then
    gpio write 4 0
    gpio write 4 1
    sleep 2
    gpio write 4 0
fi
