# Iconclass Information Service for CollectiveAccess

[InformationService](http://docs.collectiveaccess.org/wiki/Information_Services) for [CollectiveAccess](https://github.com/collectiveaccess/providence). Queries the Iconclass API and outputs the first page of hits to choose from. Adds URL. 

Call it in [Pawtucket2](https://github.com/collectiveaccess/pawtucket2), the CA Frontend (for instance in a ca_objects_default_html.php if the Metadata Element is called `Iconclass`: 

    {{{<a href="^ca_objects.iconclass.url">^ca_objects.iconclass</a>}}}

- Copy the Iconclass.php to `your_providence_install/app/lib/core/Plugins/InformationService/Iconclass.php`
- Create a Metadata Element with Iconclass as Information Service
