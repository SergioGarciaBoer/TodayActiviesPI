
async function postData(url = '', data = {}){
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Accept' : 'application/json',
            'Content-Type': 'application/json; charset=UTF-8'  
        },
        body: JSON.stringify(data)
    });
    return response.json();
}
