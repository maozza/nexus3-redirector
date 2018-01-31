# Enable old fashion nexus2 rest support for downloading artifact
A PHP script that uses the "maven way" to identify the requested asset and return redirect 301 to the requested asset 

## how to use:
copy the config-sample.php to config.php and update the URL to nexus3 base url
 
 

Accepted values are:" .  'r = "Repository", g = "Group", a = "Artifact", v = "Version", c = "Classifier" e="Extention"