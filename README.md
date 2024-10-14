# Air Filter Recommendation Tool
Recommendation tool for air filters to help reduce transmission of respitatory viruses. It is important to reiterate that air filters are only an additional layer of protection on top of measures to improve natural or mechanical ventilation in indoor settings. See https://cleanairstars.com/steps to understand where air filters fit in the steps for mitigating transmission due to inhalable aerosols.

This tool is designed to allow users to see a list of recommended air filters available on the market based on their requirements including the size of the space, number of occupants, and other attributes of the filter including noise levels at different fan settings, and wifi connectivity.  

Additional attributes in future may include the presence of a prefilter to allow for regular vacuuming and hence maintenance of air delivery rate in dusty environnments, and child safety locks which is relevant to educational settings.

The data is retrieved from a google spreadsheet with a sheet for data for each country.  See this as an example https://docs.google.com/spreadsheets/d/17j6FZwvqHRFkGoH5996u5JdR7tk4_7fNuTxAK7kc4Fk/edit?usp=sharing

Steps to setup:
- Extract the files to the root folder of your domain or sub domain or sub folder.
- Create a new project in Google Cloud Console
  - Enable the Google Sheets API
  - Create OAuth Client credentials and set domain.com/admin/auth.php as a Authorised redirect URI.
  - Copy the client ID and secret for use below.
- Verify that /config and /db directories exists, and if not, create them and verify write permissions
- Create a file config.json inside the data/config directory that looks like so:
  - {"client_id":"xxxx.apps.googleusercontent.com","client_secret":"xxx123","sheet_id":"17j6FZwvqHRFkGoH5996u5JdR7tk4_7fNuTxAK7kc4Fk","admin_email":"you@domain.com"}
  - Enter the values from your project created above, the public sheet ID can stay the same, and your email address you wish to login with.
- Visit your project domain.com/admin and login using Google Login
  - Click Settings in top menu
  - Click the Authorize with Google button next to the Refresh Token field.
  - Visit the Dashboard and click Generate Database.
