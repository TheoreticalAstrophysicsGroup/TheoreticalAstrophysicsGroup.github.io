#############################################################################
#
# Modified version of jekyllrb Rakefile
# https://github.com/jekyll/jekyll/blob/master/Rakefile
#
#############################################################################

require 'rake'
require 'date'
require 'yaml'


# Some basic Config and encrypted variables
CONFIG = YAML.load(File.read('_config.yml'))
POSTLIMIT = CONFIG['post_limit'] 
ORGNAME = CONFIG['orgname']
REPO = "#{ORGNAME}.github.io"
GITEMAIL = ENV['GIT_MAIL']
USERNAME = ENV['GIT_NAME']

# Source and destination branch. User or organization: source -> master
# Name of source branch for user/organization defaults to "source"
SOURCE_BRANCH = CONFIG['branch'] || "source"
DESTINATION_BRANCH_CCS = "astro"
DESTINATION_BRANCH_GH = "master"


#############################################################################
#
# Helper functions
#
#############################################################################

def check_destination_ccs
  unless Dir.exist? CONFIG['destination_ccs']
    sh "git clone https://#{USERNAME}:#{ENV['GH_TOKEN']}@github.com/#{ORGNAME}/#{REPO}.git #{ENV['TRAVIS_BUILD_DIR']}/#{CONFIG['destination_ccs']}"
  end
end

def check_destination_gh
  unless Dir.exist? CONFIG['destination_gh']
    sh "git clone https://#{USERNAME}:#{ENV['GH_TOKEN']}@github.com/#{ORGNAME}/#{REPO}.git #{ENV['TRAVIS_BUILD_DIR']}/#{CONFIG['destination_gh']}"
  end
end


#############################################################################
#
# Site tasks
#
#############################################################################

namespace :site do
  desc "Generate the site locally"
  task :build do
    sh "bundle exec jekyll build --future --drafts --limit_posts #{POSTLIMIT} --profile --config _config_loc.yml"
  end

  desc "Generate the site locally"
  task :build_ccs do
    sh "bundle exec jekyll build --future --drafts --limit_posts #{POSTLIMIT} --profile --config _config_ccs.yml"
  end

  desc "Generate the site to be served in xampp"
  task :xampp do
    sh "bundle exec jekyll build --future --drafts --limit_posts #{POSTLIMIT} --incremental --profile --config _config_xampp.yml"
  end

  desc "Generate the site and serve locally"
  task :serve do
    sh "bundle exec jekyll serve --trace --future --drafts --limit_posts #{POSTLIMIT} --config _config_loc.yml"
  end

  desc "Generate the site and serve locally"
  task :serve_ccs do
    sh "bundle exec jekyll serve --trace --future --drafts --limit_posts #{POSTLIMIT} --config _config_ccs.yml"
  end

  desc "Generate the site, serve locally and watch for changes"
  task :watch do
    sh "bundle exec jekyll serve --watch --future --drafts --limit_posts #{POSTLIMIT} --incremental --config _config_loc.yml"
  end

  desc "Generate the site on gorilla, and serve locally and watch for changes"
  task :gorilla do
    sh "bundle exec jekyll serve --watch --future --drafts --limit_posts #{POSTLIMIT} --detach --config _config_gor.yml"
  end

  desc "Deployment preparations"
  task :prep_ccs do

    # Detect pull request
    # if ENV['TRAVIS_PULL_REQUEST'].to_s.to_i > 0
    #   puts 'Pull request detected. Not proceeding with deploy.'
    #   exit
    # end

    # Configure git if this is run in Travis CI
    if ENV['TRAVIS']
      sh "git config --global user.name '#{USERNAME}'"
      sh "git config --global user.email '#{GITEMAIL}'"
      sh "git config --global push.default simple"
      sh 'git config --global credential.helper "cache --timeout=3600"'
    end

    # Checkout source branch
    sh "git checkout #{SOURCE_BRANCH}"

    # CCS
    check_destination_ccs
    Dir.chdir("#{ENV['TRAVIS_BUILD_DIR']}/#{CONFIG['destination_ccs']}") { sh "git checkout #{DESTINATION_BRANCH_CCS}" }

  end

  desc "Deployment preparations"
  task :prep_gh do

    # Detect pull request
    # if ENV['TRAVIS_PULL_REQUEST'].to_s.to_i > 0
    #   puts 'Pull request detected. Not proceeding with deploy.'
    #   exit
    # end

    # Configure git if this is run in Travis CI
    if ENV['TRAVIS']
      sh "git config --global user.name '#{USERNAME}'"
      sh "git config --global user.email '#{GITEMAIL}'"
      sh "git config --global push.default simple"
      sh 'git config --global credential.helper "cache --timeout=3600"'
    end

    # Github
    check_destination_gh
    Dir.chdir("#{ENV['TRAVIS_BUILD_DIR']}/#{CONFIG['destination_gh']}") { sh "git checkout #{DESTINATION_BRANCH_GH}" }

  end

  desc "Generate the site and push changes to remote astro"
  task :deploy_ccs do

    # Generate and check the site.
    sh "bundle exec jekyll build --future --limit_posts #{POSTLIMIT} --config _config_ccs.yml"
    #HTML::Proofer.new("#{ENV['TRAVIS_BUILD_DIR']}/#{CONFIG['destination_ccs']}").run

  end

  desc "Generate the site and push changes to remote master"
  task :deploy_gh do

    # Generate and check the site. baseurl must be empty. 
    sh "bundle exec jekyll build --future --limit_posts #{POSTLIMIT} --config _config_gh.yml"
    #HTML::Proofer.new("#{ENV['TRAVIS_BUILD_DIR']}/#{CONFIG['destination_gh']}").run

  end

  desc "Commit and push to github"
  task :push_ccs do

    # CCS
    sha = `git log`.match(/[a-z0-9]{40}/)[0]
    Dir.chdir("#{ENV['TRAVIS_BUILD_DIR']}/#{CONFIG['destination_ccs']}") do
      sh "git add --all ."
      sh "git commit -m 'Updating to #{ORGNAME}/#{REPO}@#{sha}.'"
      sh "git push -u --quiet origin #{DESTINATION_BRANCH_CCS}"
      puts "Pushed updated branch #{DESTINATION_BRANCH_CCS} to GitHub Pages"
    end

  end

  desc "Commit and push to github"
  task :push_gh do

    # Github
    sha = `git log`.match(/[a-z0-9]{40}/)[0]
    Dir.chdir("#{ENV['TRAVIS_BUILD_DIR']}/#{CONFIG['destination_gh']}") do
      sh "git add --all ."
      sh "git commit -m 'Updating to #{ORGNAME}/#{REPO}@#{sha}.'"
      sh "git push -u --quiet origin #{DESTINATION_BRANCH_GH}"
      puts "Pushed updated branch #{DESTINATION_BRANCH_GH} to GitHub Pages"
    end

  end

end

