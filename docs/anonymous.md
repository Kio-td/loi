# Anonymous Websocket

The Anonymous Websocket is more of a "find info" websocket, with the exception of registration, and forgetting emails/passwords.
The endpoint is ``/anon``.


An example of using the Anonymous Websocket would be:
```JSON5
{cmd: 'species'}
```

Which, aptly, would retrieve all the available species on the server.

## Reference

All commands listed follow the same format, in JSON5.

```JSON5
// Sent to server
{ 
		cmd: 'COMMAND', // The command name
		data: '' //Any optional data
}
//From the server
{
	ok: true, //if the request was successfully executed or not.
	code: 4, //Refer to codelist in the repo for the meaning.
	data: '' //Any Data the server was to return. Can be int, string, array, or object.
}
```
If you get a negative number, there will always be a message attached to the statement, under ``msg: ''``. All messages are machine friendly. You should be able to hardcode them into any third party applications.

### ``Species``

Returns the list of species to the end user, in JSON5.

Send:
```JSON5
{cmd:'species'}
```

Returns:
```JSON5
{
	ok: true,
	code: 4,
	data: [
		{
			sid: 1,
			sname: 'Human',
			atk: 5,
			def: 5,
			dex: 5,
			str: 5,
			pids: ''
			description: 'The humans are a base race for all the species.'
		}
	]
}
```

### ``Authenticate``

Returns the token, provided the username and password are correct.

Send:
```JSON5
{cmd: 'authenticate', data:{username: '', password: ''}}
```

Returns:
```JSON5
//If failed- (Email/Password is incorrect or user doesn't exist)
{
	ok: false,
	code: -4,
	msg: 'INC_DATA' // Or CONF_EMAIL if the email is unconfirmed.
}

//If successful
{
	ok: true,
	code: 4,
	data: '' //Base64 Encoded token
}
```