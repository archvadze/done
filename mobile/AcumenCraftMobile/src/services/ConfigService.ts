import Constants from 'expo-constants';
import * as Device from 'expo-device';

export interface AppConfig {
  apiBaseUrl: string;
  apiTimeout: number;
  environment: 'development' | 'staging' | 'production';
  version: string;
  buildNumber: string;
  isProduction: boolean;
  isDevelopment: boolean;
  analyticsEnabled: boolean;
  pushNotificationsEnabled: boolean;
  offlineModeEnabled: boolean;
}

class ConfigService {
  private static instance: ConfigService;
  private config: AppConfig;

  private constructor() {
    this.config = this.initializeConfig();
  }

  public static getInstance(): ConfigService {
    if (!ConfigService.instance) {
      ConfigService.instance = new ConfigService();
    }
    return ConfigService.instance;
  }

  private initializeConfig(): AppConfig {
    const isDev = __DEV__;
    const manifest = Constants.expoConfig;

    // Determine environment
    const environment = isDev ? 'development' :
      Constants.executionEnvironment === 'storeClient' ? 'production' : 'staging';

    // API Configuration based on environment
    const getApiBaseUrl = (): string => {
      switch (environment) {
        case 'production':
          return 'https://api.acumencraft.com/v1';
        case 'staging':
          return 'https://staging-api.acumencraft.com/v1';
        default:
          return 'http://done.ddev.site:33000/api/v1';
      }
    };

    return {
      apiBaseUrl: getApiBaseUrl(),
      apiTimeout: environment === 'production' ? 10000 : 30000,
      environment: environment as 'development' | 'staging' | 'production',
      version: manifest?.version || '1.0.0',
      buildNumber: manifest?.ios?.buildNumber || manifest?.android?.versionCode?.toString() || '1',
      isProduction: environment === 'production',
      isDevelopment: environment === 'development',
      analyticsEnabled: environment === 'production',
      pushNotificationsEnabled: environment !== 'development',
      offlineModeEnabled: true,
    };
  }

  public getConfig(): AppConfig {
    return this.config;
  }

  public getApiBaseUrl(): string {
    return this.config.apiBaseUrl;
  }

  public isProduction(): boolean {
    return this.config.isProduction;
  }

  public isDevelopment(): boolean {
    return this.config.isDevelopment;
  }

  public getDeviceInfo() {
    return {
      isDevice: Device.isDevice,
      brand: Device.brand,
      manufacturer: Device.manufacturer,
      modelName: Device.modelName,
      osName: Device.osName,
      osVersion: Device.osVersion,
      platformApiLevel: Device.platformApiLevel,
      deviceType: Device.deviceType,
    };
  }

  public getAppInfo() {
    return {
      name: Constants.expoConfig?.name || 'Acumen Craft',
      version: this.config.version,
      buildNumber: this.config.buildNumber,
      environment: this.config.environment,
      executionEnvironment: Constants.executionEnvironment,
      isExpoGo: Constants.executionEnvironment === 'storeClient',
    };
  }

  // Update API base URL dynamically (for testing)
  public updateApiBaseUrl(url: string): void {
    this.config.apiBaseUrl = url;
  }

  // Feature flags
  public isFeatureEnabled(feature: keyof AppConfig): boolean {
    return Boolean(this.config[feature]);
  }
}

export default ConfigService.getInstance();
