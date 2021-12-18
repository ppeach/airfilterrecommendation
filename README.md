# Air Filter Recommendation Tool
Recommendation tool for air filters to help reduce transmission of respitatory viruses. It is important to reiterate that air filters are only an additional layer of protection on top of measures to improve natural or mechanical ventilation in indoor settings. See cleanairstars.com/steps to understand where air filters fit in the steps for mitigating transmission.

This tool is designed to allow users to see a list of recommended air filters available on the market based on their requirements including the size of the space, number of occupants, and other attributes of the filter including noise levels at different fan settings, and wifi connectivity.  

Additional attributes in future may include the presence of a prefilter to allow for regular vacuuming and hence maintenance of air delivery rate in dusty environnments, and child safety locks which is relevant to educational settings.

The data is retrieved from a google spreadsheet with a sheet for data for each country 

You will need to enable google API for your google sheet.

Extract the files to the root folder of your domain or sub domain or sub folder.

Edit file config.json in the folder data/config

Add client_id, client_secret and refresh_token and save

Please make sure there are two folders (/config and /db), if db folder does not exist, create one

After that visit your domain or sub domain or domain.com/sub-folder

To update the data from google sheets you can use a URL update trigger

Replace $key value in update.php file with your generated random string

After that visit this URL to update the data domain.com/update.php?key=randomgeneratedkey
