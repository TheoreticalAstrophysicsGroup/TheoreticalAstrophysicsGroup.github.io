# Choose "seminars" or "uchu_forum" as required first argument adn additional rsync options (e.g. -n) as optional second argument.

rsync -Pavi $2 charon.ccs.tsukuba.ac.jp:~/Astro/uchu_forum/uchu_forum_form/img/ assets/img/$1/
rsync -Pavi $2 charon.ccs.tsukuba.ac.jp:~/Astro/uchu_forum/uchu_forum_form/yml/ $1/_posts/
