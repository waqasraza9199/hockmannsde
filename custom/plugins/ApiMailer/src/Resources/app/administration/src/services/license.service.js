import ApiService from 'src/core/service/api.service';

export default class LicenseService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = '') {
        super(httpClient, loginService, apiEndpoint);
    }

    async sync() {
        const response = await this.httpClient.get(
            '/lzyt-apimailer/license/sync',
            {
                headers: this.getBasicHeaders()
            }
        );
        
        return ApiService.handleResponse(response);
    }
}