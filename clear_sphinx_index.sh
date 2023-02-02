#!/bin/bash
PATH=/home/vagrant/.rvm/gems/ruby-2.3.1/wrappers:/home/vagrant/.rvm/gems/ruby-2.3.1/bin:/home/vagrant/.rvm/gems/ruby-2.3.1@global/bin:/home/vagrant/.rvm/rubies/ruby-2.3.1/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/home/vagrant/.rvm/bin:/home/vagrant/.rvm/bin
echo 'Update sphinx index structure.';
ROOT="/var/www/trade/"
CONFIG_PATH="/var/www/trade/sphinx_source.conf"
#STATUS=$(sudo searchd --status --config ${CONFIG_PATH})
sudo searchd --stop --config ${CONFIG_PATH} >> /dev/null 2>&1
#if ! [[ ${STATUS} =~ "FATAL" ]]; then
#    sudo searchd --stop --config ${CONFIG_PATH}
#fi
cd /etc/sphinxsearch/data/trade/rt && sudo rm -r -f *
sudo searchd --config ${CONFIG_PATH}
cd ${ROOT}
php yii sphinx/reindex
echo 'Ok.';