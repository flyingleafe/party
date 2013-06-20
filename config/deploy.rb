set :application, "party"
set :repository,  "git@github.com:flyingleafe/party.git"

set :scm, :git # You can set :scm explicitly or Capistrano will make an intelligent guess based on known version control directory names
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

server "82.146.59.184", :app, :web, :db, :primary => true

set :user, "root"
set :deploy_to, "/srv/www/party"
set :password, "DjT8LZA7"
set :via, "scp"
set :branch, "master"
set :deploy_via, :remote_cache
set :use_sudo, false

default_run_options[:pty] = true

#role :web, "vkusnoe-delo.ru"                          # Your HTTP server, Apache/etc
#role :app, "your app-server here"                          # This may be the same as your `Web` server
#role :db,  "your primary db-server here", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

# if you want to clean up old releases on each deploy uncomment this:
# after "deploy:restart", "deploy:cleanup"

# if you're still using the script/reaper helper you will need
# these http://github.com/rails/irs_process_scripts

# If you are using Passenger mod_rails uncomment this:
# namespace :deploy do
#   task :start do ; end
#   task :stop do ; end
#   task :restart, :roles => :app, :except => { :no_release => true } do
#     run "#{try_sudo} touch #{File.join(current_path,'tmp','restart.txt')}"
#   end
# end

# Переопределяем настройки для PHP

# namespace :deploy do
#     task :start do
#     end
#     task :stop do
#     end
#     task :restart do
#     end
#     task :finalize_update do
#       run "#{try_sudo} chown -R www-data:art /srv/www/ufaenergotrend.ru"
#       run "#{try_sudo} chmod -R 775 /srv/www/ufaenergotrend.ru"
#     end
#   end