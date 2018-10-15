FROM nginx:alpine

COPY url-parser.local.conf /etc/nginx/conf.d/url-parser.local.conf

WORKDIR /etc/nginx

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
