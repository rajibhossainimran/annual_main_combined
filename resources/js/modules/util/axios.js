const instance = axios.create({
    headers: {
        'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});

export default instance