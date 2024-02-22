var Helpers = {
    auth_url: function (url, user) {
        var key = user.id_number;
        var token = user.api_token;
        var auth_string = 'token=' + token + '&key=' + key;
        if(url.includes('?'))
            return url + '&' +auth_string
        else
            return url + '?' + auth_string
    },
    reload_after(seconds = 0) {
      setTimeout(() => {
        location.reload();
      }, seconds)
    }
}

module.exports = Helpers;
