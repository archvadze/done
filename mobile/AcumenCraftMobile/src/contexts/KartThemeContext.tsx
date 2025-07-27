import React, { createContext, useContext, ReactNode } from 'react';
import { COLORS, TYPOGRAPHY, SPACING, RADIUS, ICON_SIZES } from '../constants/config';

// ქართული თემის ინტერფეისი
interface KartTheme {
  colors: typeof COLORS;
  typography: typeof TYPOGRAPHY;
  spacing: typeof SPACING;
  radius: typeof RADIUS;
  iconSizes: typeof ICON_SIZES;

  // ქართული კომპონენტების სტილები
  components: {
    button: {
      primary: any;
      secondary: any;
      text: any;
    };
    card: {
      artwork: any;
      profile: any;
      standard: any;
    };
    input: {
      standard: any;
      search: any;
    };
  };
}

// ქართული თემის შექმნა
const createKartTheme = (): KartTheme => ({
  colors: COLORS,
  typography: TYPOGRAPHY,
  spacing: SPACING,
  radius: RADIUS,
  iconSizes: ICON_SIZES,

  components: {
    button: {
      primary: {
        backgroundColor: COLORS.gold[700],
        color: COLORS.gray[900],
        borderRadius: RADIUS.md,
        paddingVertical: SPACING.md,
        paddingHorizontal: SPACING.lg,
        fontSize: TYPOGRAPHY.sizes.base,
        fontFamily: TYPOGRAPHY.fonts.georgian.body,
        fontWeight: TYPOGRAPHY.weights.semibold,
        shadowColor: COLORS.gold[700],
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 10,
        elevation: 6,
      },
      secondary: {
        backgroundColor: 'transparent',
        color: COLORS.gold[500],
        borderColor: COLORS.gold[500],
        borderWidth: 2,
        borderRadius: RADIUS.md,
        paddingVertical: SPACING.md,
        paddingHorizontal: SPACING.lg,
        fontSize: TYPOGRAPHY.sizes.base,
        fontFamily: TYPOGRAPHY.fonts.georgian.body,
        fontWeight: TYPOGRAPHY.weights.medium,
      },
      text: {
        backgroundColor: 'transparent',
        color: COLORS.gold[500],
        fontSize: TYPOGRAPHY.sizes.base,
        fontFamily: TYPOGRAPHY.fonts.georgian.body,
        fontWeight: TYPOGRAPHY.weights.medium,
      },
    },

    card: {
      artwork: {
        backgroundColor: COLORS.gray[800],
        borderRadius: RADIUS.lg,
        padding: SPACING.lg,
        marginBottom: SPACING.lg,
        borderWidth: 1,
        borderColor: COLORS.gray[700],
        borderTopWidth: 3,
        borderTopColor: COLORS.gold[700],
        shadowColor: COLORS.black,
        shadowOffset: { width: 0, height: 6 },
        shadowOpacity: 0.15,
        shadowRadius: 12,
        elevation: 8,
      },
      profile: {
        backgroundColor: COLORS.gray[800],
        borderRadius: RADIUS.xl,
        padding: SPACING.xl,
        marginHorizontal: SPACING.lg,
        borderWidth: 2,
        borderColor: COLORS.gold[700],
        shadowColor: COLORS.gold[700],
        shadowOffset: { width: 0, height: 8 },
        shadowOpacity: 0.2,
        shadowRadius: 16,
        elevation: 12,
      },
      standard: {
        backgroundColor: COLORS.gray[800],
        borderRadius: RADIUS.md,
        padding: SPACING.lg,
        marginBottom: SPACING.md,
        borderWidth: 1,
        borderColor: COLORS.gray[700],
        shadowColor: COLORS.black,
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.1,
        shadowRadius: 8,
        elevation: 3,
      },
    },

    input: {
      standard: {
        backgroundColor: COLORS.gray[700],
        borderColor: COLORS.gray[600],
        borderWidth: 1,
        borderRadius: RADIUS.md,
        paddingVertical: SPACING.md,
        paddingHorizontal: SPACING.lg,
        fontSize: TYPOGRAPHY.sizes.base,
        color: COLORS.text,
        fontFamily: TYPOGRAPHY.fonts.georgian.body,
        // Focus state
        focusedBorderColor: COLORS.gold[600],
        focusedBorderWidth: 2,
      },
      search: {
        backgroundColor: COLORS.gray[700],
        borderRadius: RADIUS.md,
        paddingVertical: SPACING.sm,
        paddingHorizontal: SPACING.lg,
        paddingLeft: 44, // Space for search icon
        fontSize: TYPOGRAPHY.sizes.base,
        color: COLORS.text,
        fontFamily: TYPOGRAPHY.fonts.georgian.body,
      },
    },
  },
});

// ქართული თემის კონტექსტი
const KartThemeContext = createContext<KartTheme | undefined>(undefined);

// ქართული თემის პროვაიდერი
interface KartThemeProviderProps {
  children: ReactNode;
}

export const KartThemeProvider: React.FC<KartThemeProviderProps> = ({ children }) => {
  const theme = createKartTheme();

  return (
    <KartThemeContext.Provider value={theme}>
      {children}
    </KartThemeContext.Provider>
  );
};

// ქართული თემის Hook
export const useKartTheme = (): KartTheme => {
  const context = useContext(KartThemeContext);
  if (context === undefined) {
    throw new Error('useKartTheme must be used within a KartThemeProvider');
  }
  return context;
};

// Export default theme
export const kartTheme = createKartTheme();
export default kartTheme;
