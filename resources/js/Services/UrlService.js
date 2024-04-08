import axios from 'axios';

export default class UrlService {
    static async shortenUrl(originalUrl) {
        try {
            const response = await axios.post('/shorten-url', { url: originalUrl});
            return response.data;
        } catch (error) {
            console.error('Error in shortenUrl: ', error);
            throw error;
        }
    }
}
