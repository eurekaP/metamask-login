function connectMetamask(path) {
	// console.log(path);
	if (typeof window.ethereum !== 'undefined') {
		// console.log('Metamask is installed');
        ethereum.request({
            method: 'eth_requestAccounts'
        }).then((resp) => {
            window.location.href = path + '/auth/metamask?address=' + resp;
            console.log(resp);
        }).catch((err) => {
            console.log(err);
        });
	} else {
		// console.log('Metamask is not installed');
        window.location.reload();
	}
}
