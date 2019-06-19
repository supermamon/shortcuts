# AirtableKit

Make the best use of Airtable via Shortcuts.

## How to Use

### Get an API Key
You will need a personal API key. You can get one by visiting your Airtable [account page](https://airtable.com/account) under the API section. Have the API key ready when you first run the shortcut.

### Setup

With your API key ready, run the shortcut. You will be prompted with a set of steps on how to setup your configuration. Configuration includes the following items

* Creating the `/AirtableKit/` folder
* Creating the `/AirtableKit/airtable.json` file
* Storing the API key.
* (optional) Saving a list of your base IDs in the `/AirtableKit/` folder


### Usage

You can download a [demo shortcut](https://routinehub.co/shortcut/2640) by running AirtableKit and choosing `Download Demo`.

The way it works is that you will need to pass a dictionary of the function that needs to be called and the relevant data.

**Common Arguments**

|  Key |Description|
|------|------------|
|fn    |The function to call|
|base  |The base name or base ID of the relevant base|
|table |The table the is needed to be accessed|

**Functions and arguments**

`createRecord`

| Key  |Description                                             |
|------|--------------------------------------------------------|
|fields|A dictionary of keys and values for the data to be added|

`updateRecord`

|  Key    |Description                                               |
|---------|----------------------------------------------------------|
|record_id|record id of the record that needs to be updated          |
|fields   |A dictionary of keys and values for the data to be updated|

`listRecords`

|  Key |Description                                               |
|------|--------------------------------------------------------- |
|filter|*optional*. A filter expression                           |
|view  |*optional*. View name of a custom view defined in Airtable|

`getRecord`

|  Key    |Description                                       |
|---------|--------------------------------------------------|
|record_id|record id of the record that needs to be accessed |

`deleteRecord`

|  Key    |Description                                       |
|---------|--------------------------------------------------|
|record_id|record id of the record that needs to be deleted  |


### Roadmap

* ~~Save API Key~~
* ~~Basic CRUD~~
* ~~Cache base list~~
* Attachments
* Cached tables list


### Links

* [Airtable](https://airtable.com)
* [My Shortcuts in ScPL](https://github.com/supermamon/shortcuts)
* [ScPL](https://scpl.dev/)
* [Shortcuts Discord](https://discordapp.com/invite/rw8FSaq)
