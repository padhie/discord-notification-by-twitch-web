rm /etc/nginx/sites-available/default
ln -s /application/tools/docker/nginx-site.conf /etc/nginx/sites-available/default
service nginx restart