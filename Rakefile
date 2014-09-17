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


# Some basic Config and encrypted variables
CONFIG = YAML.load(File.read('_config.yml'))
ORGNAME = CONFIG["orgname"]
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
  unless Dir.exist? CONFIG["destination"]
    sh "git clone https://#{USERNAME}:#{ENV['GH_TOKEN']}@github.com/#{ORGNAME}/#{REPO}.git #{CONFIG["destination_ccs"]}"
    #Dir.chdir(CONFIG["destination_ccs"]) { sh 'git config --local credential.helper "cache --timeout=3600"' }
  end
end

def check_destination_gh
  unless Dir.exist? CONFIG["destination_gh"]
    sh "git clone https://#{USERNAME}:#{ENV['GH_TOKEN']}@github.com/#{ORGNAME}/#{REPO}.git #{CONFIG["destination_gh"]}"
    #Dir.chdir(CONFIG["destination_gh"]) { sh 'git config --local credential.helper "cache --timeout=3600"' }
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

  desc "Generate sites for deployment on ccs and gh"
  multitask :deploy => ['site:deploy_ccs', 'site:deploy_gh']

  desc "Generate the site and push changes to remote astro"
  task :deploy_ccs do

    # Detect pull request
    #if ENV['TRAVIS_PULL_REQUEST'].to_s.to_i > 0
    #  puts 'Pull request detected. Not proceeding with deploy.'
    #  exit
    #end

    # Configure git if this is run in Travis CI
    if ENV["TRAVIS"]
      sh "git config --global user.name '#{USERNAME}'"
      sh "git config --global user.email '#{GITEMAIL}'"
      sh "git config --global push.default simple"
      sh 'git config --global credential.helper "cache --timeout=3600"'
    end

    # Make sure destination folder exists as git repo
    check_destination_ccs

    sh "git checkout #{SOURCE_BRANCH}"
    Dir.chdir(CONFIG["destination_ccs"]) { sh "git checkout #{DESTINATION_BRANCH_CCS}" }

    # Generate the site
    sh "bundle exec jekyll build"

    # Check build
    HTML::Proofer.new("CONFIG['destination_ccs']").run

    # Commit and push to github and rsync to charon
    sha = `git log`.match(/[a-z0-9]{40}/)[0]
    Dir.chdir(CONFIG["destination_ccs"]) do
      sh "git add --all ."
      sh "git commit -m 'Updating to #{ORGNAME}/#{REPO}@#{sha}.'"
      sh "git push -u --quiet origin #{DESTINATION_BRANCH_CCS}"
      puts "Pushed updated branch #{DESTINATION_BRANCH_CCS} to GitHub Pages"
    end
  end

  desc "Generate the site and push changes to remote master"
  task :deploy_gh do

    # Just delay this slightly
    sh "sleep 10"

    # Detect pull request
    #if ENV['TRAVIS_PULL_REQUEST'].to_s.to_i > 0
    #  puts 'Pull request detected. Not proceeding with deploy.'
    #  exit
    #end

    # Configure git if this is run in Travis CI
    #if ENV["TRAVIS"]
    #  sh "git config --global user.name '#{USERNAME}'"
    #  sh "git config --global user.email '#{GITEMAIL}'"
    #  sh "git config --global push.default simple"
    #  sh 'git config --local credential.helper "cache --timeout=3600"'
    #end

    # Make sure destination folder exists as git repo
    check_destination_gh

    #sh "git checkout #{SOURCE_BRANCH}"
    Dir.chdir(CONFIG["destination_gh"]) { sh "git checkout #{DESTINATION_BRANCH_GH}" }

    # Generate the site. Baseurl must be empty. 
    sh "bundle exec jekyll build --baseurl ''"

    # Check build
    HTML::Proofer.new("CONFIG['destination_gh']").run

    # Commit and push to github and rsync to charon
    sha = `git log`.match(/[a-z0-9]{40}/)[0]
    Dir.chdir(CONFIG["destination_gh"]) do
      sh "git add --all ."
      sh "git commit -m 'Updating to #{ORGNAME}/#{REPO}@#{sha}.'"
      sh "git push -u --quiet origin #{DESTINATION_BRANCH_GH}"
      puts "Pushed updated branch #{DESTINATION_BRANCH_GH} to GitHub Pages"
    end
  end

end

