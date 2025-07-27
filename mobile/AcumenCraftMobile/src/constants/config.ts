// API Configuration and Constants with Production Support

// Environment-based API Configuration
const getApiConfig = () => {
  const isDev = __DEV__;

  if (isDev) {
    // Development environment
    return {
      BASE_URL: 'http://done.ddev.site:33000/api/v1',
      TIMEOUT: 30000,
      RETRY_ATTEMPTS: 3,
    };
  } else {
    // Production environment
    return {
      BASE_URL: 'https://api.acumencraft.com/v1',
      TIMEOUT: 10000,
      RETRY_ATTEMPTS: 2,
    };
  }
};

export const API_CONFIG = {
  ...getApiConfig(),

  // API Endpoints
  ENDPOINTS: {
    // Authentication
    LOGIN: '/auth/login',
    REGISTER: '/auth/register',
    LOGOUT: '/auth/logout',
    REFRESH: '/auth/refresh',
    PROFILE: '/auth/profile',

    // Artworks
    ARTWORKS: '/artworks',
    ARTWORK_DETAIL: (id: string) => `/artworks/${id}`,
    ARTWORK_UPLOAD: '/artworks',
    ARTWORK_LIKE: (id: string) => `/artworks/${id}/like`,
    ARTWORK_COMMENTS: (id: string) => `/artworks/${id}/comments`,

    // Evaluations
    EVALUATIONS: '/evaluations',
    EVALUATION_CREATE: '/evaluations',
    LEADERBOARD: '/leaderboard',

    // Users
    USER_PROFILE: (id: string) => `/users/${id}`,
    USER_ARTWORKS: (id: string) => `/users/${id}/artworks`,

    // Communities
    COMMUNITIES: '/communities',
    COMMUNITY_DETAIL: (id: string) => `/communities/${id}`,
    COMMUNITY_JOIN: (id: string) => `/communities/${id}/join`,
    COMMUNITY_POSTS: (id: string) => `/communities/${id}/posts`,

    // Support
    FAQ: '/support/faq',
    SUPPORT_TICKETS: '/support/tickets',
    HELP_ARTICLES: '/support/help',
  },

  // Request Headers
  HEADERS: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-App-Version': '1.0.0',
    'X-Platform': 'mobile',
  }
};

// App Configuration with Production Settings
export const CONFIG = {
  APP_NAME: 'AcumenCraft Mobile',
  API_URL: __DEV__
    ? 'http://done.ddev.site:33000/api/v1'
    : 'https://api.acumencraft.com/v1',
  TIMEOUT: 10000,
  MAX_RETRY_ATTEMPTS: 3,
  CACHE_DURATION: 5 * 60 * 1000, // 5 minutes

  // Feature Flags
  FEATURES: {
    CAMERA_UPLOAD: true,
    SOCIAL_FEATURES: true,
    OFFLINE_MODE: true,
    ANALYTICS: !__DEV__,
    CRASH_REPORTING: !__DEV__,
  },

  // UI Configuration
  UI: {
    ANIMATION_DURATION: 300,
    DEBOUNCE_DELAY: 500,
    PAGINATION_SIZE: 20,
  },

  // Security
  SECURITY: {
    JWT_EXPIRY_BUFFER: 5 * 60 * 1000, // 5 minutes
    MAX_LOGIN_ATTEMPTS: 5,
  },
};

// ქართული ფერების პალიტრა
export const COLORS = {
  // ქართული ოქროს ტრადიციული გამა
  gold: {
    900: '#8B5A2B',  // მუქი ქართული ოქრო
    800: '#A0651F',  // ძირითადი მუქი ოქრო
    700: '#C28840',  // მთავარი ბრენდ ოქრო
    600: '#D4A743',  // სტანდარტული ოქრო
    500: '#F0C75E',  // ღია ოქრო
    400: '#F3D374',  // ნაცარი ოქრო
    300: '#F6DF8B',  // ოქროს ჩრდილი
    200: '#FAECA8',  // ძალიან ღია ოქრო
    100: '#FDF6C4',  // ოქროს ფონი
  },

  // თანამედროვე ნაცრისფერი
  gray: {
    900: '#0A0A0A',  // ღია შავი
    800: '#1A1A1A',  // ძირითადი მუქი
    700: '#2A2A2A',  // მუქი ნაცარი
    600: '#404040',  // საშუალო მუქი
    500: '#5A5A5A',  // ნეიტრალური
    400: '#757575',  // საშუალო ღია
    300: '#909090',  // ღია ნაცარი
    200: '#B5B5B5',  // ძალიან ღია
    100: '#E0E0E0',  // თითქმის თეთრი
    50: '#FAFAFA',   // ღია თეთრი
  },

  // სემანტიკური ფერები
  success: {
    500: '#22C55E',
    100: '#DCFCE7',
  },
  warning: {
    500: '#F59E0B',
    100: '#FEF3C7',
  },
  error: {
    500: '#EF4444',
    100: '#FEE2E2',
  },
  info: {
    500: '#3B82F6',
    100: '#DBEAFE',
  },

  // ქართული ბრენდის ძირითადი ფერები
  primary: '#C28840',     // მთავარი ბრენდ ოქრო
  secondary: '#F0C75E',   // ღია ოქრო
  background: '#0A0A0A',  // ღია შავი ფონი
  surface: '#1A1A1A',    // კარტების ფონი
  text: '#FAFAFA',       // ღია თეთრი ტექსტი
  textSecondary: '#B5B5B5', // ღია ნაცარი ტექსტი
  border: '#2A2A2A',     // მუქი ნაცარი ბორდერი
  white: '#FAFAFA',
  black: '#0A0A0A',
};

// ქართული ტიპოგრაფიული სისტემა
export const TYPOGRAPHY = {
  // ქართული შრიფტები
  fonts: {
    georgian: {
      primary: 'BPG ExtraSquare Caps',
      body: 'BPG ExtraSquare',
      fallback: 'NovaSquare',
    },
    latin: 'Inter',
    mono: 'JetBrains Mono',
  },

  // ქართული ტექსტის ზომები
  sizes: {
    // ქართული სათაურები
    '6xl': 64,   // მთავარი სათაური
    '5xl': 51,   // გვერდის სათაური
    '4xl': 40,   // სექციის სათაური
    '3xl': 32,   // ქვე-სათაური
    '2xl': 26,   // კარტის სათაური
    'xl': 21,    // დიდი ტექსტი

    // ქართული ტექსტი
    'lg': 18,    // დიდი ძირითადი
    'base': 16,  // ძირითადი ტექსტი
    'sm': 14,    // პატარა ტექსტი
    'xs': 13,    // კაპციები
  },

  // შრიფტის წონები
  weights: {
    light: '300',
    normal: '400',
    medium: '500',
    semibold: '600',
    bold: '700',
    extrabold: '800',
  },

  // ქართული ტექსტის სიმაღლე
  lineHeights: {
    tight: 1.2,
    normal: 1.4,
    relaxed: 1.6,
  },
};

// ქართული სპეიცინგი და ზომები
export const SPACING = {
  xs: 6,     // ქართული ასოებისთვის
  sm: 10,
  md: 18,
  lg: 26,
  xl: 36,
  xxl: 52,
  xxxl: 72,
};

export const RADIUS = {
  sm: 6,     // პატარა ელემენტები
  md: 12,    // კარტები, ღილაკები
  lg: 16,    // დიდი კარტები
  xl: 20,    // მოდალები
  full: 999, // წრიული ელემენტები
};

// ქართული ხატულების ზომები
export const ICON_SIZES = {
  xs: 14,    // პატარა inline ხატულები
  sm: 18,    // სტანდარტული ტექსტის ხატულები
  md: 22,    // ღილაკის ხატულები
  lg: 28,    // ნავიგაციის ხატულები
  xl: 36,    // დიდი feature ხატულები
  xxl: 52,   // hero ხატულები
};

// Error Messages
export const ERROR_MESSAGES = {
  NETWORK_ERROR: 'Network connection error. Please check your internet connection.',
  SERVER_ERROR: 'Server error. Please try again later.',
  UNAUTHORIZED: 'You are not authorized to perform this action.',
  VALIDATION_ERROR: 'Please check your input and try again.',
  FILE_TOO_LARGE: 'File size is too large. Maximum allowed size is 10MB.',
  UNSUPPORTED_FILE_TYPE: 'Unsupported file type.',
  GENERIC_ERROR: 'Something went wrong. Please try again.',
};

// Success Messages
export const SUCCESS_MESSAGES = {
  LOGIN_SUCCESS: 'Successfully logged in!',
  REGISTER_SUCCESS: 'Account created successfully!',
  ARTWORK_UPLOADED: 'Artwork uploaded successfully!',
  ARTWORK_UPDATED: 'Artwork updated successfully!',
  ARTWORK_DELETED: 'Artwork deleted successfully!',
  EVALUATION_SUBMITTED: 'Evaluation submitted successfully!',
  PROFILE_UPDATED: 'Profile updated successfully!',
};
