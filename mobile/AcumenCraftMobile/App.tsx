import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { StatusBar } from 'expo-status-bar';
import { Ionicons } from '@expo/vector-icons';

// Providers
import { AuthProvider } from './src/context/AuthContext';
import { KartThemeProvider, useKartTheme } from './src/contexts/KartThemeContext';

// Screens
import LoginScreen from './src/screens/auth/LoginScreen';
import RegisterScreen from './src/screens/auth/RegisterScreen';
import HomeScreen from './src/screens/main/HomeScreen';
import ExploreScreen from './src/screens/main/ExploreScreen';
import UploadScreen from './src/screens/upload/EnhancedUploadScreen';
import CommunityScreen from './src/screens/community/CommunitiesScreen';
import ProfileScreen from './src/screens/profile/ProfileScreen';
import ArtworkDetailScreen from './src/screens/artwork/ArtworkDetailScreen';

// Types
import { RootStackParamList, MainTabParamList } from './src/types';

const Stack = createStackNavigator<RootStackParamList>();
const Tab = createBottomTabNavigator<MainTabParamList>();

// Main Tab Navigator with Georgian Theme
const MainTabNavigator = () => {
  const theme = useKartTheme();

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          let iconName: keyof typeof Ionicons.glyphMap;

          if (route.name === 'Home') {
            iconName = focused ? 'home' : 'home-outline';
          } else if (route.name === 'Explore') {
            iconName = focused ? 'search' : 'search-outline';
          } else if (route.name === 'Upload') {
            iconName = focused ? 'add-circle' : 'add-circle-outline';
          } else if (route.name === 'Communities') {
            iconName = focused ? 'people' : 'people-outline';
          } else if (route.name === 'Profile') {
            iconName = focused ? 'person' : 'person-outline';
          } else {
            iconName = 'home-outline';
          }

          return <Ionicons name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: theme.colors.gold[600],      // ქართული ოქრო
        tabBarInactiveTintColor: theme.colors.gray[400],    // ნაცარი
        tabBarStyle: {
          backgroundColor: theme.colors.gray[800],          // მუქი ფონი
          borderTopColor: theme.colors.gray[700],           // ზედა ბორდერი
          borderTopWidth: 1,
          paddingBottom: 8,
          paddingTop: 8,
          height: 70,
        },
        tabBarLabelStyle: {
          fontFamily: theme.typography.fonts.georgian.body,
          fontSize: 12,
          fontWeight: '500',
        },
        headerShown: false,
      })}
    >
      <Tab.Screen name="Home" component={HomeScreen} options={{ title: 'მთავარი' }} />
      <Tab.Screen name="Explore" component={ExploreScreen} options={{ title: 'აღმოჩენა' }} />
      <Tab.Screen name="Upload" component={UploadScreen} options={{ title: 'ატვირთვა' }} />
      <Tab.Screen name="Communities" component={CommunityScreen} options={{ title: 'თემები' }} />
      <Tab.Screen name="Profile" component={ProfileScreen} options={{ title: 'პროფილი' }} />
    </Tab.Navigator>
  );
};

// Root Navigator with Georgian Theme
const RootNavigator = () => {
  const theme = useKartTheme();

  return (
    <Stack.Navigator
      initialRouteName="Login"
      screenOptions={{
        headerStyle: {
          backgroundColor: theme.colors.gray[800],
          borderBottomColor: theme.colors.gray[700],
          borderBottomWidth: 1,
        },
        headerTintColor: theme.colors.gold[500],
        headerTitleStyle: {
          fontFamily: theme.typography.fonts.georgian.body,
          fontWeight: '600',
          fontSize: 18,
        },
        headerBackTitleStyle: {
          fontFamily: theme.typography.fonts.georgian.body,
        },
      }}
    >
      <Stack.Screen
        name="Login"
        component={LoginScreen}
        options={{ headerShown: false }}
      />
      <Stack.Screen
        name="Register"
        component={RegisterScreen}
        options={{ title: 'ანგარიშის შექმნა' }}
      />
      <Stack.Screen
        name="MainTabs"
        component={MainTabNavigator}
        options={{ headerShown: false }}
      />
      <Stack.Screen
        name="ArtworkDetail"
        component={ArtworkDetailScreen}
        options={{ title: 'ნამუშევრის დეტალები' }}
      />
    </Stack.Navigator>
  );
};

// Main App Component
export default function App() {
  return (
    <KartThemeProvider>
      <AuthProvider>
        <NavigationContainer>
          <RootNavigator />
          <StatusBar style="light" backgroundColor="#0A0A0A" />
        </NavigationContainer>
      </AuthProvider>
    </KartThemeProvider>
  );
}
