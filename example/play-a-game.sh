#!/bin/bash

if ! command -v curl &> /dev/null
then
    echo "curl could not be found"
    exit
fi

if ! command -v jq &> /dev/null
then
    echo "jq could not be found"
    exit
fi

# create a game
gameId=$(curl -s -X POST http://127.0.0.1:8000/game | jq -r '.gameId')

playUrl="http://127.0.0.1:8000/game/${gameId}"

printf "player 1 moves 1,1 \n"
curl -s -X PATCH "$playUrl" -d player=1 -d x=1 -d y=1 | jq .
printf "\n\n"

printf "player 2 moves 0,2 \n"
curl -s -X PATCH "$playUrl" -d player=2 -d x=0 -d y=2 | jq .
printf "\n\n"

printf "player 1 moves 2,1 \n"
curl -s -X PATCH "$playUrl" -d player=1 -d x=2 -d y=1 | jq .
printf "\n\n"

printf "player 2 moves 1,2 \n"
curl -s -X PATCH "$playUrl" -d player=2 -d x=1 -d y=2 | jq .
printf "\n\n"

printf "player 1 moves 0,1 and wins \n"
curl -s -X PATCH "$playUrl" -d player=1 -d x=0 -d y=1 | jq .
printf "\n\n"
