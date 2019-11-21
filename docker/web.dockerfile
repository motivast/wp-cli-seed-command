FROM nginx:1.10

COPY ./docker/web/virtualhost.conf /etc/nginx/conf.d/default.conf
