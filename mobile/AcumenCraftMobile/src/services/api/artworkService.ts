import { API_CONFIG } from '../../constants/config';
import {
  Artwork,
  ArtworkCreateRequest,
  ApiResponse,
  PaginatedResponse,
  Comment,
  CommentRequest
} from '../../types';

export class ArtworkService {
  private getAuthHeaders(): Record<string, string> {
    // This will be enhanced to get token from storage
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
  }

  // Get all artworks with pagination
  async getArtworks(page: number = 1, perPage: number = 20): Promise<PaginatedResponse<Artwork>> {
    try {
      const response = await fetch(
        `${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.ARTWORKS}?page=${page}&per_page=${perPage}`,
        {
          method: 'GET',
          headers: this.getAuthHeaders(),
        }
      );

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to fetch artworks');
      }

      return data;
    } catch (error) {
      console.error('Get artworks error:', error);
      throw error;
    }
  }

  // Get single artwork by ID
  async getArtwork(id: number): Promise<Artwork> {
    try {
      const response = await fetch(
        `${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.ARTWORK_DETAIL(id.toString())}`,
        {
          method: 'GET',
          headers: this.getAuthHeaders(),
        }
      );

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to fetch artwork');
      }

      return data.data;
    } catch (error) {
      console.error('Get artwork error:', error);
      throw error;
    }
  }

  // Create new artwork
  async createArtwork(artworkData: ArtworkCreateRequest): Promise<Artwork> {
    try {
      const formData = new FormData();

      // Add text fields
      formData.append('title_en', artworkData.title_en);
      if (artworkData.title_de) formData.append('title_de', artworkData.title_de);
      if (artworkData.title_ka) formData.append('title_ka', artworkData.title_ka);

      formData.append('description_en', artworkData.description_en);
      if (artworkData.description_de) formData.append('description_de', artworkData.description_de);
      if (artworkData.description_ka) formData.append('description_ka', artworkData.description_ka);

      formData.append('tags', artworkData.tags);
      formData.append('license', artworkData.license);
      formData.append('is_ai_generated', artworkData.is_ai_generated.toString());
      formData.append('visibility', artworkData.visibility);

      if (artworkData.copyright_notice) {
        formData.append('copyright_notice', artworkData.copyright_notice);
      }

      // Add file
      formData.append('media_file', artworkData.media_file as any);

      const response = await fetch(
        `${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.ARTWORK_UPLOAD}`,
        {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            // Don't set Content-Type for FormData, let the browser set it
          },
          body: formData,
        }
      );

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to create artwork');
      }

      return data.data;
    } catch (error) {
      console.error('Create artwork error:', error);
      throw error;
    }
  }

  // Like/unlike artwork
  async toggleLike(artworkId: number): Promise<{ liked: boolean; likes_count: number }> {
    try {
      const response = await fetch(
        `${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.ARTWORK_LIKE(artworkId.toString())}`,
        {
          method: 'POST',
          headers: this.getAuthHeaders(),
        }
      );

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to toggle like');
      }

      return data.data;
    } catch (error) {
      console.error('Toggle like error:', error);
      throw error;
    }
  }

  // Get artwork comments
  async getArtworkComments(artworkId: number): Promise<Comment[]> {
    try {
      const response = await fetch(
        `${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.ARTWORK_COMMENTS(artworkId.toString())}`,
        {
          method: 'GET',
          headers: this.getAuthHeaders(),
        }
      );

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to fetch comments');
      }

      return data.data;
    } catch (error) {
      console.error('Get comments error:', error);
      throw error;
    }
  }

  // Add comment to artwork
  async addComment(commentData: CommentRequest): Promise<Comment> {
    try {
      const response = await fetch(
        `${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.ARTWORK_COMMENTS(commentData.artwork_id.toString())}`,
        {
          method: 'POST',
          headers: this.getAuthHeaders(),
          body: JSON.stringify(commentData),
        }
      );

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to add comment');
      }

      return data.data;
    } catch (error) {
      console.error('Add comment error:', error);
      throw error;
    }
  }

  // Search artworks
  async searchArtworks(
    query: string,
    filters?: {
      tags?: string[];
      media_type?: string;
      user_id?: number;
    }
  ): Promise<Artwork[]> {
    try {
      const searchParams = new URLSearchParams({
        q: query,
      });

      if (filters?.tags?.length) {
        searchParams.append('tags', filters.tags.join(','));
      }
      if (filters?.media_type) {
        searchParams.append('media_type', filters.media_type);
      }
      if (filters?.user_id) {
        searchParams.append('user_id', filters.user_id.toString());
      }

      const response = await fetch(
        `${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.ARTWORKS}/search?${searchParams.toString()}`,
        {
          method: 'GET',
          headers: this.getAuthHeaders(),
        }
      );

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Search failed');
      }

      return data.data;
    } catch (error) {
      console.error('Search artworks error:', error);
      throw error;
    }
  }

  // Upload artwork
  async uploadArtwork(data: any): Promise<Artwork> {
    try {
      const formData = new FormData();

      // Add file
      if (data.file) {
        formData.append('media', {
          uri: data.file.uri,
          type: data.file.type || 'image/jpeg',
          name: data.file.name || 'artwork.jpg',
        } as any);
      }

      // Add metadata
      formData.append('title', JSON.stringify(data.title));
      formData.append('description', JSON.stringify(data.description));
      formData.append('tags', data.tags);
      formData.append('license', data.license);
      formData.append('visibility', data.visibility);
      formData.append('category', data.category);
      formData.append('is_ai_generated', data.is_ai_generated.toString());

      const response = await fetch(
        `${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.ARTWORKS}`,
        {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            // Don't set Content-Type for FormData, let the browser set it
          },
          body: formData,
        }
      );

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.message || 'Failed to upload artwork');
      }

      return result.data;
    } catch (error) {
      console.error('Failed to upload artwork:', error);
      throw error;
    }
  }
}
