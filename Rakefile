#############################################################################
#
# Modified version of jekyllrb Rakefile
# https://github.com/jekyll/jekyll/blob/master/Rakefile
#
#############################################################################

require 'rake'
require 'date'
require 'yaml'
require 'html/proofer'


CONFIG = YAML.load(File.read('_config.yml'))
USERNAME = CONFIG["username"]
ORGNAME = CONFIG["orgname"]
GITEMAIL = CONFIG["gitemail"]
#USERNAME = CONFIG["username"] || ENV['GIT_NAME']
#ORGNAME = CONFIG["orgname"] || ENV['ORG_NAME']
REPO = CONFIG["repo"] || "#{ORGNAME}.github.io"

# Determine source and destination branch
# User or organization: source -> master
# Project: master -> gh-pages
# Name of source branch for user/organization defaults to "source"
if REPO == "#{ORGNAME}.github.io" || REPO ==  "#{USERNAME}.github.io"
  SOURCE_BRANCH = CONFIG['branch'] || "source"
  DESTINATION_BRANCH = "master"
else
  SOURCE_BRANCH = "master"
  DESTINATION_BRANCH = "gh-pages"
end


#############################################################################
#
# Helper functions
#
#############################################################################

def check_destination
  unless Dir.exist? CONFIG["destination"]
    sh "git clone https://#{USERNAME}:#{ENV['GH_TOKEN']}@github.com/#{ORGNAME}/#{REPO}.git #{CONFIG["destination"]}"
    #sh "git clone https://#{ENV['GIT_NAME']}:#{ENV['GH_TOKEN']}@github.com/#{ORGNAME}/#{REPO}.git #{CONFIG["destination"]}"
    Dir.chdir(CONFIG["destination"]) { sh 'git config --local credential.helper "cache --timeout=3600"' }
  end
end


#############################################################################
#
# Site tasks
#
#############################################################################

namespace :site do
  desc "Generate the site"
  task :build do
    check_destination
    sh "bundle exec jekyll build"
  end

  desc "Generate the site and serve locally"
  task :serve do
    check_destination
    sh "bundle exec jekyll serve"
  end

  desc "Generate the site, serve locally and watch for changes"
  task :watch do
    sh "bundle exec jekyll serve --watch"
  end

  desc "Generate the site and push changes to remote origin"
  task :deploy do

    # Detect pull request
    if ENV['TRAVIS_PULL_REQUEST'].to_s.to_i > 0
      puts 'Pull request detected. Not proceeding with deploy.'
      exit
    end

    # Configure git if this is run in Travis CI
    if ENV["TRAVIS"]
      sh "git config --global user.name '#{USERNAME}'"
      sh "git config --global user.email '#{GITEMAIL}'"
      sh "git config --global push.default simple"
      sh 'git config --local credential.helper "cache --timeout=3600"'
    end

    # Make sure destination folder exists as git repo
    check_destination

    sh "git checkout #{SOURCE_BRANCH}"
    Dir.chdir(CONFIG["destination"]) { sh "git checkout #{DESTINATION_BRANCH}" }

    # Generate the site. Add a random output so that travis won't timeout
    sh "./summat.sh"
    sh "bundle exec jekyll build --verbose"

    # Check build
    HTML::Proofer.new("CONFIG['destination']").run

    # Commit and push to github and rsync to charon
    sha = `git log`.match(/[a-z0-9]{40}/)[0]
    Dir.chdir(CONFIG["destination"]) do
      sh "git add --all ."
      sh "git commit -m 'Updating to #{ORGNAME}/#{REPO}@#{sha}.'"
      sh "git push -u --quiet origin #{DESTINATION_BRANCH}"
      puts "Pushed updated branch #{DESTINATION_BRANCH} to GitHub Pages"
      sh "sshpass -p #{ENV['CCS_PW']} rsync -Prvi --exclude='.git' --exclude='.gitignore' * #{ENV['CCS_NAME']}@charon.ccs.tsukuba.ac.jp:/home-WWW/Research/Astro/"
    end
  end
end

