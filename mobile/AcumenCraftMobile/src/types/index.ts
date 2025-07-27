// User Types
export interface User {
  id: number;
  name: string;
  email: string;
  avatar_url?: string;
  bio?: string;
  country?: string;
  lang: string;
  creative_field?: string;
  is_admin: boolean;
  status: 'active' | 'banned' | 'pending' | 'deleted';
  created_at: string;
  updated_at: string;
}

// Authentication Types
export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  lang?: string;
  creative_field?: string;
}

export interface AuthResponse {
  token: string;
  refresh_token: string;
  user: User;
  expires_in: number;
}

// Artwork Types
export interface Artwork {
  id: number;
  user_id: number;
  title: Record<string, string>; // Multi-language titles
  description: Record<string, string>; // Multi-language descriptions
  media_url: string;
  media_type: 'image' | 'audio' | 'video' | 'other';
  tags: string;
  license: string;
  copyright_notice?: string;
  is_ai_generated: boolean;
  nft_token_id?: string;
  blockchain?: string;
  visibility: 'public' | 'private' | 'draft';
  acq_score?: number;
  likes_count: number;
  comments_count: number;
  views_count: number;
  user: User;
  created_at: string;
  updated_at: string;
}

export interface ArtworkCreateRequest {
  title_en: string;
  title_de?: string;
  title_ka?: string;
  description_en: string;
  description_de?: string;
  description_ka?: string;
  media_file: File | FormData;
  tags: string;
  license: string;
  copyright_notice?: string;
  is_ai_generated: boolean;
  visibility: 'public' | 'private' | 'draft';
}

// Evaluation Types
export interface Evaluation {
  id: number;
  artwork_id: number;
  user_id?: number;
  technique: number; // 1-10
  composition: number; // 1-10
  originality: number; // 1-10
  impact: number; // 1-10
  feedback?: string;
  type: 'manual' | 'ai';
  created_at: string;
}

export interface EvaluationRequest {
  artwork_id: number;
  technique: number;
  composition: number;
  originality: number;
  impact: number;
  feedback?: string;
}

// Comment Types
export interface Comment {
  id: number;
  artwork_id: number;
  user_id: number;
  content: string;
  status: 'visible' | 'hidden' | 'deleted';
  user: User;
  created_at: string;
}

export interface CommentRequest {
  artwork_id: number;
  content: string;
}

// Community Types
export interface Community {
  id: number;
  name: string;
  description: string;
  avatar_url?: string;
  is_private: boolean;
  members_count: number;
  posts_count: number;
  created_at: string;
}

export interface CommunityPost {
  id: number;
  community_id: number;
  user_id: number;
  title: string;
  content: string;
  likes_count: number;
  comments_count: number;
  user: User;
  created_at: string;
}

// API Response Types
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
  status?: number;
}

// Navigation Types
export type RootStackParamList = {
  // Auth Stack
  Login: undefined;
  Register: undefined;
  ForgotPassword: undefined;

  // Main Stack
  MainTabs: undefined;

  // Artwork Stack
  ArtworkDetail: { artworkId: number };
  ArtworkUpload: undefined;
  ArtworkEdit: { artworkId: number };
  Upload: undefined;

  // User Stack
  Profile: { userId?: number };
  Settings: undefined;

  // Community Stack
  CommunityDetail: { communityId: number };
  CommunityPost: { postId: number };

  // Evaluation Stack
  EvaluationForm: { artworkId: number };
  Leaderboard: undefined;
};

export type MainTabParamList = {
  Home: undefined;
  Explore: undefined;
  Upload: undefined;
  Communities: undefined;
  Profile: undefined;
};

// Store Types (for state management)
export interface AppState {
  auth: AuthState;
  artworks: ArtworkState;
  user: UserState;
  ui: UIState;
}

export interface AuthState {
  isAuthenticated: boolean;
  token?: string;
  refreshToken?: string;
  user?: User;
  loading: boolean;
  error?: string;
}

export interface ArtworkState {
  artworks: Artwork[];
  currentArtwork?: Artwork;
  loading: boolean;
  error?: string;
  pagination: {
    currentPage: number;
    lastPage: number;
    total: number;
  };
}

export interface UserState {
  profile?: User;
  loading: boolean;
  error?: string;
}

export interface UIState {
  theme: 'light' | 'dark' | 'system';
  language: string;
  isConnected: boolean;
}

// Form Types
export interface FormField {
  value: string;
  error?: string;
  touched: boolean;
}

export interface LoginForm {
  email: FormField;
  password: FormField;
}

export interface RegisterForm {
  name: FormField;
  email: FormField;
  password: FormField;
  passwordConfirmation: FormField;
  creativeField: FormField;
}
