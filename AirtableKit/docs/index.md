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

**Format of the `fields` parameter**

`fields` is a dictionary containg the Airtable field names as keys and their corresponding values.
A simple `fields` value would be something like this:

```json
{
    "Field1": "value1",
    "Field2": "value2",
    "Field3": "value3"
}
```

In case of values that are linked to records to another table, you usually pass an array of the `id`s of the related records. Example:

```json
{
    "Field1": "value1",
    "Field2": "value2",
    "Field3": "value3",
    "LinkedValue": [
        "reqQead8349"
    ]
}
```

**AirtableKit** allows this, it would mean that you need the `id`s beforehand.
As of v1.3.0, it is easier to add linked records when creating or updating records.

```json
{
    "Field1": "value1",
    "Field2": "value2",
    "Field3": "value3",
    "LinkedValue.linked": {
        "table" : "Another Table",
        "filters" : [
            "xfield1='xvalue1'",
            "xfield2='xvalue2'"
        ]
    }        
}
```

The format of the linked field would be as follow:

Key     : The fieldname suffixed by `.linked` to signify AirtableKit that this is a linked fields.
Value   : A dictionary containing 2 keys, (2) the name of the `table` where to find the linked record, and (2) an array of `filters` to locate the linked record.


*Table1*

| T1Col1  | T1Col2             |
|---------|--------------------|
| Value1  |Some Description    |
| Value2  |Another Description |

*Table2* - `T2Col2` is a link to a record in Table1

| T2Col1  | T2Col2  |
|---------|---------|
| V1.1    |Value1   |
| V1.2    |Value1   |
| V2.1    |Value2   |

If we were to insert a record in *Table2* for example, the `fields` parameter would be structured like this:

```json
{
    "T2Col1": "V2.2",
    "T2Col2.linked": {
        "table" : "Table1",
        "filters" : [
            "T1Col1='Value2'"
        ]
    }        
}
```


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
