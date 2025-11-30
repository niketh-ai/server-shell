export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method === 'POST') {
    const { action, user_key, hwid, days } = req.body;
    
    if (action === 'create_license') {
      // For now, just return success
      // Later you can save to database
      return res.json({
        success: true,
        message: 'License created successfully',
        expiry_date: new Date(Date.now() + days * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
      });
    }
  }

  return res.status(405).json({ error: 'Method not allowed' });
}