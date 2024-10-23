import { IsEmail, IsNotEmpty, IsString, IsOptional } from 'class-validator';
//DTO da mensagem com biblioteca class-validator
export class CreateMessageDto {
  @IsNotEmpty()
  @IsString()
  name: string;

  @IsEmail()
  @IsNotEmpty()
  email: string;

  @IsOptional()
  phone?: string;

  @IsNotEmpty()
  @IsString()
  message: string;

  @IsOptional()
  ddd?: number; 
}