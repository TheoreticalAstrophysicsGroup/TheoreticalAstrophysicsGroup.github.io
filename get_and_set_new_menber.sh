#!/bin/bash

#change username and add key place if neeeded
registerd_member_directory="temporary_data_from_charon/new_registerd_member"
lastyear_members_directory="_members/lastyear_members/"
members_directory="_members"
almuni_directory="alumni/maybe_alumni_list"
scp -r -q yuasat@charon.ccs.tsukuba.ac.jp:/home-WWW/Research/Astro/membersform_data/*.html $registerd_member_directory

goodbye_members=()
# 登録されたメンバーをメンバーディレクトリに格納
for file in "$registerd_member_directory"/*; do
    # charonからとってきたファイル(メンバー)を_memberディレクトリに格納
    filename=$(basename "$file")
     # ファイルが既に存在しない場合のみコピーする
    if [ ! -f "$members_directory/ja/$filename" ]; then
        cp "$registerd_member_directory/$filename" "$members_directory/ja/$filename"

        # ファイルの末尾にlang, order, roles, profile_pic, footnoteを追加する
        # 2個目の"---"の前の行に挿入
        var=$(awk '/---/ {print NR}' $members_directory/ja/$filename)
        row=$(echo $var | awk '{print $2}')
        # start_lineとend_lineの間に行を挿入する
        sed -i "${row}i \
lang: ja\\
order: \\
roles: \\
profile_pic: \\
footnote: " "$members_directory/ja/$filename"

        cp "$registerd_member_directory/$filename" "$members_directory/en/$filename"

        var=$(awk '/---/ {print NR}' $members_directory/en/$filename)
        row=$(echo $var | awk '{print $2}')
        # ファイルの末尾にlang, order, roles, profile_pic, footnoteを追加する
        sed -i "${row}i \
lang: en\\
order: \\
roles: \\
profile_pic: \\
footnote: " "$members_directory/en/$filename"
    # ファイルが既に存在する場合は、項目上書き
    else
     #ファイルが存在する場合は、name, email, tel, position, homepageを置換
      var=$(awk '/---/ {print NR}' $members_directory/ja/$filename)
      row=$(echo $var | awk '{print $1}')

      name=$(awk '/^name/{print}' "$registerd_member_directory/$filename")
      name1=$(echo "$name" | awk 'NR==1{print}')
      name2=$(echo "$name" | awk 'NR==2{print}')
      email=$(awk '/^email/{print}' "$registerd_member_directory/$filename")
      tel=$(awk '/^tel/{print}' "$registerd_member_directory/$filename")
      position=$(awk '/^position/{print}' "$registerd_member_directory/$filename")
      homepage=$(awk '/^homepage/{print}' "$registerd_member_directory/$filename")
      research=$(awk '/^research/{print}' "$registerd_member_directory/$filename")

      sed -i -e "/^name/d" "$members_directory/ja/$filename"
      sed -i -e "/^email/d" "$members_directory/ja/$filename"
      sed -i -e "/^tel/d" "$members_directory/ja/$filename"
      sed -i -e "/^position/d" "$members_directory/ja/$filename"
      sed -i -e "/^homepage/d" "$members_directory/ja/$filename"
      sed -i -e "/^research/d" "$members_directory/ja/$filename"

      sed -i "${row}a\\
${research}" "$members_directory/ja/$filename"
      sed -i "${row}a\\
${homepage}" "$members_directory/ja/$filename"
      sed -i "${row}a\\
${position}" "$members_directory/ja/$filename"
      sed -i "${row}a\\
${tel}" "$members_directory/ja/$filename"
      sed -i "${row}a\\
${email}" "$members_directory/ja/$filename"
      sed -i "${row}a\\
${name2}" "$members_directory/ja/$filename"
      sed -i "${row}a\\
${name1}" "$members_directory/ja/$filename"

        
    fi
    
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
        cp "$lastyear_members_directory/ja/$file" "$almuni_directory/ja/$file"
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
        cp "$lastyear_members_directory/en/$file" "$almuni_directory/en/$file"
    done
fi

