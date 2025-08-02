
function callApi(url, method, jsonData) {
    return fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: method === 'GET' || method === 'HEAD' ? undefined : JSON.stringify(jsonData)
    })
        .then(res => res.json());
}


