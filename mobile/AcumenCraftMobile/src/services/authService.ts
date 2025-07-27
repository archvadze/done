import AsyncStorage from '@react-native-async-storage/async-storage';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';
import { API_CONFIG, APP_CONFIG } from '../constants/config';
import {
  LoginRequest,
  RegisterRequest,
  AuthResponse,
  User,
  ApiResponse
} from '../types';

export class AuthService {
  private isSecureStoreAvailable = Platform.OS !== 'web';

  // Token management using SecureStore for sensitive data
  async setToken(token: string): Promise<void> {
    if (this.isSecureStoreAvailable) {
      await SecureStore.setItemAsync(APP_CONFIG.STORAGE_KEYS.AUTH_TOKEN, token);
    } else {
      await AsyncStorage.setItem(APP_CONFIG.STORAGE_KEYS.AUTH_TOKEN, token);
    }
  }

  async getToken(): Promise<string | null> {
    if (this.isSecureStoreAvailable) {
      return await SecureStore.getItemAsync(APP_CONFIG.STORAGE_KEYS.AUTH_TOKEN);
    } else {
      return await AsyncStorage.getItem(APP_CONFIG.STORAGE_KEYS.AUTH_TOKEN);
    }
  }

  async setRefreshToken(refreshToken: string): Promise<void> {
    if (this.isSecureStoreAvailable) {
      await SecureStore.setItemAsync(APP_CONFIG.STORAGE_KEYS.REFRESH_TOKEN, refreshToken);
    } else {
      await AsyncStorage.setItem(APP_CONFIG.STORAGE_KEYS.REFRESH_TOKEN, refreshToken);
    }
  }

  async getRefreshToken(): Promise<string | null> {
    if (this.isSecureStoreAvailable) {
      return await SecureStore.getItemAsync(APP_CONFIG.STORAGE_KEYS.REFRESH_TOKEN);
    } else {
      return await AsyncStorage.getItem(APP_CONFIG.STORAGE_KEYS.REFRESH_TOKEN);
    }
  }

  // User data management using AsyncStorage
  async setUserData(user: User): Promise<void> {
    await AsyncStorage.setItem(
      APP_CONFIG.STORAGE_KEYS.USER_DATA,
      JSON.stringify(user)
    );
  }

  async getUserData(): Promise<User | null> {
    const userData = await AsyncStorage.getItem(APP_CONFIG.STORAGE_KEYS.USER_DATA);
    return userData ? JSON.parse(userData) : null;
  }

  // Authentication state check
  async isAuthenticated(): Promise<boolean> {
    const token = await this.getToken();
    return !!token;
  }

  // Clear all auth data
  async clearAuthData(): Promise<void> {
    const keys = [
      APP_CONFIG.STORAGE_KEYS.AUTH_TOKEN,
      APP_CONFIG.STORAGE_KEYS.REFRESH_TOKEN,
      APP_CONFIG.STORAGE_KEYS.USER_DATA,
    ];

    if (this.isSecureStoreAvailable) {
      await Promise.all([
        SecureStore.deleteItemAsync(APP_CONFIG.STORAGE_KEYS.AUTH_TOKEN).catch(() => { }),
        SecureStore.deleteItemAsync(APP_CONFIG.STORAGE_KEYS.REFRESH_TOKEN).catch(() => { }),
        AsyncStorage.removeItem(APP_CONFIG.STORAGE_KEYS.USER_DATA),
      ]);
    } else {
      await AsyncStorage.multiRemove(keys);
    }
  }

  // API methods - integrated with backend
  async login(credentials: LoginRequest): Promise<AuthResponse> {
    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.LOGIN}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(credentials),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Login failed');
      }

      // Store tokens and user data
      await this.setToken(data.token);
      await this.setRefreshToken(data.refresh_token);
      await this.setUserData(data.user);

      return data;
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    }
  }

  async register(userData: RegisterRequest): Promise<AuthResponse> {
    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.REGISTER}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(userData),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Registration failed');
      }

      // Store tokens and user data
      await this.setToken(data.token);
      await this.setRefreshToken(data.refresh_token);
      await this.setUserData(data.user);

      return data;
    } catch (error) {
      console.error('Registration error:', error);
      throw error;
    }
  }

  async logout(): Promise<void> {
    await this.clearAuthData();
    // Additional logout logic if needed (API call to invalidate token)
  }

  async refreshToken(): Promise<string> {
    const refreshToken = await this.getRefreshToken();
    if (!refreshToken) {
      throw new Error('No refresh token available');
    }

    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.REFRESH}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ refresh_token: refreshToken }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Token refresh failed');
      }

      await this.setToken(data.token);
      if (data.refresh_token) {
        await this.setRefreshToken(data.refresh_token);
      }

      return data.token;
    } catch (error) {
      console.error('Token refresh error:', error);
      await this.clearAuthData();
      throw error;
    }
  }

  async getCurrentUser(): Promise<User | null> {
    // Try to get from storage first
    const userData = await this.getUserData();
    if (userData) {
      return userData;
    }

    // If not in storage and we have a token, fetch from API
    const token = await this.getToken();
    if (token) {
      try {
        const response = await fetch(`${API_CONFIG.BASE_URL}${API_CONFIG.ENDPOINTS.PROFILE}`, {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          },
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.message || 'Failed to fetch user profile');
        }

        await this.setUserData(data.user);
        return data.user;
      } catch (error) {
        console.error('Get current user error:', error);
        await this.clearAuthData();
        throw error;
      }
    }

    return null;
  }

  // Initialize auth state on app start
  async initializeAuth(): Promise<{
    isAuthenticated: boolean;
    user: User | null;
  }> {
    const isAuth = await this.isAuthenticated();
    const user = isAuth ? await this.getUserData() : null;

    return {
      isAuthenticated: isAuth,
      user,
    };
  }
}
