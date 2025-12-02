export default async function handler(req, res) {
    const { url } = req.query;

    if (!url) {
        return res.status(400).json({ error: 'Missing URL' });
    }

    try {
        const parsedUrl = new URL(url);

        // Normalize host - remove www.
        let host = parsedUrl.hostname.toLowerCase();
        host = host.replace(/^www\./, '');

        // Allowed domains (without www)
        const allowedHosts = ['seeintl.org', 'waapple.org', 'snopud.com', 'cchpca.org'];

        if (!allowedHosts.includes(host)) {
            return res.status(403).json({ error: 'URL not allowed' });
        }

        const response = await fetch(url);

        if (!response.ok) {
            return res.status(response.status).json({ error: 'Request failed' });
        }

        const contentType = response.headers.get('content-type');
        const body = await response.text();

        res.setHeader('Content-Type', contentType);
        res.status(200).send(body);

    } catch (error) {
        console.error('Proxy error:', error);
        return res.status(500).json({ error: 'Internal Server Error' });
    }
}
