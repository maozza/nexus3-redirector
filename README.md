# Enable old fashioned nexus2 rest support for downloading maven assets.
A PHP script that uses the "maven way" to identify the requested asset and return redirect 301 to the location of the asset.

## how to use:
copy the config-sample.php to config.php and update the URL to nexus3 base URL


## nginx example configuration

```
server {
     listen   *:80;
     server_name  nexus2.example.com;
     client_max_body_size 1G;
     root   /usr/share/nginx/html/ ;
     # nexus2 rest call for asset search been sent to php script that return a redirect to the real location
     # the location below is the location of nexus2 old api
     location /nexus/service/local/artifact/maven/content/ {
        try_files $uri $uri/ index.php;
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
     }
     # all requests send as proxy to nexus3 
     location / {
         proxy_pass http://localhost:8081;
         proxy_set_header Host $host;
         proxy_set_header X-Real-IP $remote_addr;
         proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
     }

}
```

## creare the directory structure:
```
mkdir -p /usr/share/nginx/html/nexus/service/local/artifact/maven/content && \
 cd /usr/share/nginx/html/nexus/service/local/artifact/maven/content 
```
copy the code to the above location and update the config.php



## Accepted input parameters are:
r = "Repository"
g = "Group"
a = "Artifact"
v = "Version"
c = "Classifier"
e="Extention"

