#!/bin/bash

#change username and add key place if neeeded
registerd_member_directory="__temp/new_registerd_member/"
members_directory="_members"

mkdir -p $registerd_member_directory
mkdir -p $members_directory/ja
mkdir -p $members_directory/en

scp -r -q charon:/home-WWW/Research/Astro/Astro_source/membersform_data/*.html $registerd_member_directory

goodbye_members=()
# 登録されたメンバーをメンバーディレクトリに格納
for file in "$registerd_member_directory"/*; do
    # charonからとってきたファイル(メンバー)を_memberディレクトリに格納
    filename=$(basename "$file")
    
    cp "$registerd_member_directory/$filename" "$members_directory/ja/$filename"

    # ファイルの末尾にlang, order, roles, profile_pic, footnoteを追加する
    # 2個目の"---"の前の行に挿入
    var=$(awk '/---/ {print NR}' $members_directory/ja/$filename)
    row=$(echo $var | awk '{print $2}')
    # start_lineとend_lineの間に行を挿入する

    awk 'BEGIN {count=0}
    /name:/ {count++}
    count!=2
    count==2 {count++}' $members_directory/ja/$filename > temp && mv temp $members_directory/ja/$filename
    
    awk 'BEGIN {count=0; insert="lang: ja\norder: \nroles: \nprofile_pic:\nfootnote:"}
    /---/ {count++}
    count==2 {print insert; count++}
    {print}' $members_directory/ja/$filename > temp && mv temp $members_directory/ja/$filename

    sed '/homepage: ""/d' $members_directory/ja/$filename > temp && mv temp $members_directory/ja/$filename
    

    
    cp "$registerd_member_directory/$filename" "$members_directory/en/$filename"

    var=$(awk '/---/ {print NR}' $members_directory/en/$filename)
    row=$(echo $var | awk '{print $2}')
    # ファイルの末尾にlang, order, roles, profile_pic, footnoteを追加する
    awk 'BEGIN {count=0; insert="lang: en\norder: \nroles: \nprofile_pic:\nfootnote:"}
    /---/ {count++}
    count==2 {print insert; count++}
    {print}' $members_directory/en/$filename > temp && mv temp $members_directory/en/$filename

    sed '/homepage: ""/d' $members_directory/en/$filename > temp && mv temp $members_directory/en/$filename
# ファイルが既に存在する場合は、項目上書き
    
done

