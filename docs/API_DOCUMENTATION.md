# RESTful API Documentation

## Overview
This document describes the RESTful API endpoints for the Artwork Management System with ACQ (Artwork Quality) evaluation.

## Authentication
The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

## Base URL
```
/api/v1/
```

## Endpoints

### 🎨 Artworks

#### GET /api/v1/artworks
Get list of published artworks

**Parameters:**
- `search` (optional) - Search in titles and descriptions
- `category` (optional) - Filter by category
- `ai_generated` (optional) - Filter AI generated artworks
- `page` (optional) - Page number for pagination

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title_en": "Artwork Title",
      "title_ka": "ნამუშევრის სათაური",
      "description_en": "Artwork description",
      "category": "digital-art",
      "user": {
        "id": 1,
        "name": "Artist Name",
        "avatar_path": "/avatars/user.jpg"
      },
      "likes_count": 5,
      "acq_score": 8.5,
      "created_at": "2024-01-01T12:00:00Z",
      "updated_at": "2024-01-01T12:00:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1,
    "has_more": false
  }
}
```

#### GET /api/v1/artworks/{id}
Get single artwork details

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title_en": "Artwork Title",
    "title_ka": "ნამუშევრის სათაური",
    "description_en": "Artwork description",
    "category": "digital-art",
    "file_path": "/uploads/artwork.jpg",
    "user": {
      "id": 1,
      "name": "Artist Name"
    },
    "likes_count": 5,
    "acq_score": 8.5,
    "evaluations_count": 3,
    "created_at": "2024-01-01T12:00:00Z"
  }
}
```

#### POST /api/v1/artworks 🔒
Create new artwork (requires authentication)

**Request:**
```json
{
  "title_en": "Artwork Title",
  "title_ka": "ნამუშევრის სათაური",
  "description_en": "Artwork description",
  "category": "digital-art",
  "license_type": "all_rights_reserved",
  "visibility": "public",
  "tags": ["tag1", "tag2"],
  "is_ai_generated": false,
  "comments_enabled": true,
  "downloads_enabled": false,
  "file": "base64_encoded_file_or_multipart"
}
```

#### PUT /api/v1/artworks/{id} 🔒
Update artwork (requires authentication, owner only)

#### DELETE /api/v1/artworks/{id} 🔒
Delete artwork (requires authentication, owner only)

#### POST /api/v1/artworks/{id}/like 🔒
Like/unlike artwork (requires authentication)

#### POST /api/v1/artworks/{id}/publish 🔒
Publish artwork (requires authentication, owner only)

#### POST /api/v1/artworks/{id}/unpublish 🔒
Unpublish artwork (requires authentication, owner only)

### 📊 Evaluations

#### GET /api/v1/artworks/{artwork_id}/evaluations
Get evaluations for specific artwork

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "originality_score": 8,
      "technical_score": 7,
      "aesthetic_score": 9,
      "concept_score": 8,
      "overall_score": 8,
      "acq_score": 8.2,
      "comment": "Great artwork!",
      "user": {
        "id": 2,
        "name": "Evaluator Name"
      },
      "created_at": "2024-01-01T12:00:00Z"
    }
  ]
}
```

#### POST /api/v1/artworks/{artwork_id}/evaluations 🔒
Create evaluation for artwork (requires authentication)

**Request:**
```json
{
  "originality_score": 8,
  "technical_score": 7,
  "aesthetic_score": 9,
  "concept_score": 8,
  "overall_score": 8,
  "comment": "Great artwork!"
}
```

#### GET /api/v1/evaluations/{id}
Get single evaluation

#### PUT /api/v1/evaluations/{id} 🔒
Update evaluation (requires authentication, owner only)

#### DELETE /api/v1/evaluations/{id} 🔒
Delete evaluation (requires authentication, owner only)

### 🏆 Leaderboard

#### GET /api/v1/leaderboard
Get top artworks by ACQ score

**Parameters:**
- `limit` (optional, default: 10) - Number of artworks to return

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title_en": "Top Artwork",
      "acq_score": 9.2,
      "evaluations_count": 15,
      "likes_count": 25,
      "user": {
        "id": 1,
        "name": "Top Artist"
      }
    }
  ]
}
```

### 👤 Users

#### GET /api/v1/me 🔒
Get current user profile (requires authentication)

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "avatar_path": "/avatars/user.jpg",
    "artworks_count": 5,
    "evaluations_count": 12,
    "average_acq_score": 7.8,
    "created_at": "2024-01-01T12:00:00Z"
  }
}
```

#### PUT /api/v1/me 🔒
Update current user profile (requires authentication)

**Request:**
```json
{
  "name": "Updated Name",
  "bio": "Artist bio",
  "website": "https://artist-website.com"
}
```

#### GET /api/v1/users/{id}/artworks
Get artworks by specific user

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "This action is unauthorized."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found."
}
```

### 422 Validation Error
```json
{
  "success": false,
  "errors": {
    "title_en": ["The title en field is required."],
    "file": ["The file field is required."]
  }
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "An error occurred while processing your request."
}
```

## ACQ Scoring System

The ACQ (Artwork Quality) score is calculated using the following formula:

```
ACQ Score = (
  originality_score * 0.25 +
  technical_score * 0.25 +
  aesthetic_score * 0.25 +
  concept_score * 0.25
) * (overall_score / 10)
```

Scores range from 1-10, and the final ACQ score is a weighted average of all evaluations for an artwork.

## Rate Limiting

API requests are rate-limited to prevent abuse:
- Authenticated users: 100 requests per minute
- Guest users: 20 requests per minute

## Pagination

List endpoints support pagination with the following parameters:
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15, max: 100)

## CORS

Cross-Origin Resource Sharing (CORS) is enabled for all API endpoints.

---

**Note:** 🔒 indicates endpoints that require authentication
