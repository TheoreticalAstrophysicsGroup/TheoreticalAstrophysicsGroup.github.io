# Choose 
# 1) "seminars" or "uchu_forum" as required first argument 
# 2) additional rsync options (e.g. -n) as optional second argument

if [[ "$1" == "uchu_forum" ]] || [[ "$1" == "seminars" ]]; then
  rsync -Pavi $2 charon.ccs.tsukuba.ac.jp:~/Astro/uchu_forum/uchu_forum_form/img/ assets/img/$1/
  rsync -Pavi $2 charon.ccs.tsukuba.ac.jp:~/Astro/uchu_forum/uchu_forum_form/yml/ $1/_posts/

else
  echo "Error: Invalid first parameter."

fi

