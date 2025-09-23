# Music Locker API Documentation

## Spotify Web API Integration

### Application Details
- **Client ID**: `356702eb81d0499381fcf5222ab757fb`
- **Client Secret**: `3a826c32f5dc41e9939b4ec3229a5647`
- **App Status**: Development mode
- **Redirect URI**: `http://127.0.0.1:8888/api/spotify/callback` (Updated for Spotify 2025 security requirements)
- **Scopes Required**: `user-read-private user-read-email`

### OAuth 2.0 Authorization Code Flow (2025 Best Practices)

#### Step 1: Authorization Request
```
GET https://accounts.spotify.com/authorize
```

**Parameters:**
- `client_id`: 356702eb81d0499381fcf5222ab757fb
- `response_type`: code
- `redirect_uri`: http://127.0.0.1:8888/api/spotify/callback
- `scope`: user-read-private user-read-email
- `state`: [random_string_for_csrf_protection]
- `show_dialog`: false (optional)

**Example Authorization URL:**
```
https://accounts.spotify.com/authorize?client_id=356702eb81d0499381fcf5222ab757fb&response_type=code&redirect_uri=http%3A%2F%2F127.0.0.1%3A8888%2Fapi%2Fspotify%2Fcallback&scope=user-read-private%20user-read-email&state=random_state_string
```

#### Step 2: Token Exchange
```
POST https://accounts.spotify.com/api/token
```

**Headers:**
- `Authorization`: Basic [base64(client_id:client_secret)]
- `Content-Type`: application/x-www-form-urlencoded

**Body Parameters:**
- `grant_type`: authorization_code
- `code`: [authorization_code_from_callback]
- `redirect_uri`: http://127.0.0.1:8888/api/spotify/callback

**Response:**
```json
{
  "access_token": "NgCXRKc...MzYjw",
  "token_type": "Bearer",
  "expires_in": 3600,
  "refresh_token": "NgAagA...Um_SHo",
  "scope": "user-read-private user-read-email"
}
```

#### Step 3: Token Refresh
```
POST https://accounts.spotify.com/api/token
```

**Headers:**
- `Authorization`: Basic [base64(client_id:client_secret)]
- `Content-Type`: application/x-www-form-urlencoded

**Body Parameters:**
- `grant_type`: refresh_token
- `refresh_token`: [stored_refresh_token]

### API Endpoints Used

#### Search for Music
```
GET https://api.spotify.com/v1/search
```

**Parameters:**
- `q`: Search query
- `type`: track,artist,album
- `limit`: 20 (default)
- `offset`: 0 (default)

**Headers:**
- `Authorization`: Bearer [access_token]

#### Get Track Details
```
GET https://api.spotify.com/v1/tracks/{id}
```

**Headers:**
- `Authorization`: Bearer [access_token]

#### Get Artist Details
```
GET https://api.spotify.com/v1/artists/{id}
```

**Headers:**
- `Authorization`: Bearer [access_token]

#### Get Album Details
```
GET https://api.spotify.com/v1/albums/{id}
```

**Headers:**
- `Authorization`: Bearer [access_token]

### Rate Limiting & Error Handling

#### Rate Limits
- **Standard Rate Limit**: 100 requests per minute per application
- **Extended Mode**: Higher limits available for approved apps
- **Retry-After Header**: Indicates seconds to wait before retry

#### Common Error Responses
```json
{
  "error": {
    "status": 401,
    "message": "The access token expired"
  }
}
```

#### Error Status Codes
- **400**: Bad Request - Invalid query parameters
- **401**: Unauthorized - Invalid or expired token
- **403**: Forbidden - Request forbidden by application/user settings
- **404**: Not Found - Resource not found
- **429**: Too Many Requests - Rate limit exceeded
- **500**: Internal Server Error - Spotify server error
- **502**: Bad Gateway - Service temporarily overloaded
- **503**: Service Unavailable - Service temporarily down

### Security Considerations

#### Token Storage
- **Access Token**: Store in secure session, expires in 1 hour
- **Refresh Token**: Store securely in database, encrypted
- **State Parameter**: Use cryptographically secure random strings
- **CSRF Protection**: Validate state parameter on callback

#### Best Practices
1. **Always use HTTPS** in production
2. **Validate all callback parameters**
3. **Implement token refresh automation**
4. **Handle rate limits gracefully**
5. **Log security events**
6. **Never expose client secret** in frontend code

### Implementation Notes

#### PHP cURL Example
```php
function makeSpotifyRequest($endpoint, $accessToken, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/' . $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}
```

#### Token Refresh Logic
```php
function refreshSpotifyToken($refreshToken) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode(SPOTIFY_CLIENT_ID . ':' . SPOTIFY_CLIENT_SECRET),
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}
```

### Development Environment

#### Local Testing
- **Base URL**: http://127.0.0.1:8888
- **Callback URL**: http://127.0.0.1:8888/api/spotify/callback
- **XAMPP Configuration**: Virtual host setup required

#### Required PHP Extensions
- `curl` - For API requests
- `openssl` - For HTTPS requests
- `json` - For JSON parsing (usually built-in)
- `mbstring` - For string handling

### Troubleshooting

#### Common Issues
1. **"Invalid redirect URI"**
   - Ensure exact match in Spotify app settings
   - URL encode special characters

2. **"Invalid client"**
   - Verify client ID and secret
   - Check base64 encoding of credentials

3. **"Access token expired"**
   - Implement automatic token refresh
   - Handle 401 responses gracefully

4. **Rate limiting**
   - Implement exponential backoff
   - Respect Retry-After header

### Spotify 2025 Security Changes

**Important Migration Information:**
- **Effective April 9, 2025**: New Spotify applications automatically enforce new redirect URI validation
- **Migration Deadline**: All existing applications must migrate by November 2025
- **Localhost Deprecated**: Redirect URIs containing "localhost" are no longer supported
- **HTTPS Required**: All redirect URIs must use HTTPS, except for loopback IP addresses
- **Loopback Exception**: HTTP allowed for explicit IP addresses (`127.0.0.1`, `[::1]`)

**Development Setup:**
- Use `http://127.0.0.1:8888/api/spotify/callback` for local development
- Avoid using `http://localhost:8888/api/spotify/callback` (deprecated)
- Ensure Spotify application settings match the exact redirect URI
- For production, use HTTPS redirect URIs only

---

*Documentation updated for Spotify Web API v1 (2025)*  
*Last updated: August 28, 2025*