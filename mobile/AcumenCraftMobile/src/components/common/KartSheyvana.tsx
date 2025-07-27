import React, { useState } from 'react';
import {
  TextInput,
  View,
  TouchableOpacity,
  ViewStyle,
  TextInputProps,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useKartTheme } from '../../contexts/KartThemeContext';
import KartTeqsti from './KartTeqsti';

interface KartSheyavanaProps extends Omit<TextInputProps, 'style'> {
  satauri?: string;                          // სათაური
  shecsavlia?: string;                       // შეცსავლია (error message)
  varianti?: 'standartuli' | 'zebna';        // ვარიანტი
  surathuli?: keyof typeof Ionicons.glyphMap; // სურათული (icon)
  surathulisAdgili?: 'marcxena' | 'marjvena'; // სურათულის ადგილი
  surathulisGadaadgileba?: () => void;       // სურათულის გადაადგილება
  parolisMnishvneloba?: boolean;             // პაროლის მნიშვნელობა
  stili?: ViewStyle;                         // სტილი
  sheyvanisSitli?: any;                      // შეყვანის სტილი
}

export const KartSheyvana: React.FC<KartSheyavanaProps> = ({
  satauri,
  shecsavlia,
  varianti = 'standartuli',
  surathuli,
  surathulisAdgili = 'marcxena',
  surathulisGadaadgileba,
  parolisMnishvneloba = false,
  stili,
  sheyvanisSitli,
  ...inputProps
}) => {
  const theme = useKartTheme();
  const [parolisNaxva, setParolisNaxva] = useState(false);

  // ვარიანტის სტილის მიღება
  const getVariantStyle = () => {
    const variantMap = {
      'standartuli': 'standard',
      'zebna': 'search',
    } as const;

    const mappedVariant = variantMap[varianti] as keyof typeof theme.components.input;
    return theme.components.input[mappedVariant];
  };

  const inputStyle = getVariantStyle();
  const hasError = !!shecsavlia;

  // კონტეინერის სტილი
  const containerStyle: ViewStyle = {
    marginBottom: theme.spacing.md,
  };

  // შეყვანის ველის სტილი
  const textInputStyle = {
    ...inputStyle,
    borderColor: hasError
      ? theme.colors.error[500]
      : inputStyle.borderColor,
    paddingLeft: surathuli && surathulisAdgili === 'marcxena'
      ? theme.spacing.xl + theme.spacing.md
      : inputStyle.paddingHorizontal,
    paddingRight: (surathuli && surathulisAdgili === 'marjvena') || parolisMnishvneloba
      ? theme.spacing.xl + theme.spacing.md
      : inputStyle.paddingHorizontal,
  };

  return (
    <View style={[containerStyle, stili]}>
      {satauri && (
        <KartTeqsti
          shvilebi={satauri}
          varianti="patara"
          wona="sashualod"
          feri={theme.colors.gold[500]}
          stili={{ marginBottom: theme.spacing.xs }}
        />
      )}

      <View style={{ position: 'relative' }}>
        <TextInput
          style={[textInputStyle, sheyvanisSitli]}
          placeholderTextColor={theme.colors.gray[500]}
          secureTextEntry={parolisMnishvneloba && !parolisNaxva}
          {...inputProps}
        />

        {/* მარცხენა ხატულა */}
        {surathuli && surathulisAdgili === 'marcxena' && (
          <TouchableOpacity
            style={{
              position: 'absolute',
              left: theme.spacing.md,
              top: '50%',
              transform: [{ translateY: -theme.iconSizes.sm / 2 }],
            }}
            onPress={surathulisGadaadgileba}
          >
            <Ionicons
              name={surathuli}
              size={theme.iconSizes.sm}
              color={theme.colors.gray[400]}
            />
          </TouchableOpacity>
        )}

        {/* მარჯვენა ხატულა */}
        {surathuli && surathulisAdgili === 'marjvena' && (
          <TouchableOpacity
            style={{
              position: 'absolute',
              right: theme.spacing.md,
              top: '50%',
              transform: [{ translateY: -theme.iconSizes.sm / 2 }],
            }}
            onPress={surathulisGadaadgilება}
          >
            <Ionicons
              name={surathuli}
              size={theme.iconSizes.sm}
              color={theme.colors.gray[400]}
            />
          </TouchableOpacity>
        )}

        {/* პაროლის ნახვის ღილაკი */}
        {parolisMnishvneloba && (
          <TouchableOpacity
            style={{
              position: 'absolute',
              right: theme.spacing.md,
              top: '50%',
              transform: [{ translateY: -theme.iconSizes.sm / 2 }],
            }}
            onPress={() => setParolisNaxva(!parolisNaxva)}
          >
            <Ionicons
              name={parolisNaxva ? 'eye-off' : 'eye'}
              size={theme.iconSizes.sm}
              color={theme.colors.gray[400]}
            />
          </TouchableOpacity>
        )}
      </View>

      {/* შეცდომის შეტყობინება */}
      {hasError && (
        <KartTeqsti
          shvilebi={shecsavlia}
          varianti="dzalianPatara"
          feri={theme.colors.error[500]}
          stili={{ marginTop: theme.spacing.xs }}
        />
      )}
    </View>
  );
};

export default KartSheyvana;
