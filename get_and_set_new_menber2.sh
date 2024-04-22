#!/bin/bash

#change username and add key place if neeeded
registerd_member_directory="__temp/new_registerd_member/"
lastyear_members_directory="_members/lastyear_members/"
members_directory="_members"
almuni_directory="alumni/"
scp -r -q yuasat@charon.ccs.tsukuba.ac.jp:/home-WWW/Research/Astro/Astro_source/membersform_data/*.html $registerd_member_directory

goodbye_members=()
# 登録されたメンバーをメンバーディレクトリに格納
for file in "$registerd_member_directory"/*; do
    # charonからとってきたファイル(メンバー)を_memberディレクトリに格納
    filename=$(basename "$file")
    
     # ファイルが既に存在しない場合のみコピーする
    
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

# 昨年度にいたメンバーについてループ処理
# goodbye_member内の人物は、新年度に上がる際にいなくなった人たち --> alumniに入れる
# これに関しては、全員が__membersに登録できたことを確認できた段階でやってください(昨年度にいた人で、今年度まだ登録してない人がいるとその人もalmuniに入ってしまいます)
# 念のためalumni/maybe_alumni_listにalumni判定された人入れてます
if [ "$*" = "--update_alumni" ]; then
    for file in "$lastyear_members_directory"/ja/*; do
        # ファイル名(メンバー名)のみを取得
        filename=$(basename "$file")
        # 昨年度にいたメンバーが今年度いなくなっていた場合、goodbye_member配列に入れる
        if [ ! -e "$members_directory/ja/$filename" ]; then
            goodbye_members+=("$filename")
        fi
    done

    for file in "${goodbye_members[@]}"; do
        cp "$lastyear_members_directory/ja/$file" "$almuni_directory/ja/from_members/$file"
    done



    for file in "$lastyear_members_directory"/en/*; do
        # ファイル名(メンバー名)のみを取得
        filename=$(basename "$file")
        # 昨年度にいたメンバーが今年度いなくなっていた場合、goodbye_member配列に入れる
        if [ ! -e "$members_directory/en/$filename" ]; then
            goodbye_members+=("$filename")
        fi
    done

    for file in "${goodbye_members[@]}"; do
        cp "$lastyear_members_directory/en/$file" "$almuni_directory/en/from_members/$file"
    done
fi

